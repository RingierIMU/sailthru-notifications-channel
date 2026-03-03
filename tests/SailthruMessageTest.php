<?php

use NotificationChannels\Sailthru\SailthruMessage;

// ── Fluent builder methods (TEST-02) ────────────────────────────────

test('constructor sets template', function () {
    $message = new SailthruMessage('welcome');

    expect($message->getTemplate())->toBe('welcome');
});

test('template method overrides constructor template', function () {
    $message = new SailthruMessage('old');
    $message->template('new');

    expect($message->getTemplate())->toBe('new');
});

test('vars method sets variables', function () {
    $message = new SailthruMessage('tpl');
    $result = $message->vars(['key' => 'value']);

    expect($result)->toBeInstanceOf(SailthruMessage::class)
        ->and($message->getVars())->toBe(['key' => 'value']);
});

test('eVars method sets email variables', function () {
    $message = new SailthruMessage('tpl');
    $eVars = ['user@test.com' => ['name' => 'Test']];
    $result = $message->eVars($eVars);

    expect($result)->toBeInstanceOf(SailthruMessage::class)
        ->and($message->getEVars())->toBe($eVars);
});

test('toEmail method sets recipient email', function () {
    $message = new SailthruMessage('tpl');
    $message->toEmail('user@test.com');

    expect($message->getToEmail())->toBe('user@test.com');
});

test('fromEmail method sets sender email', function () {
    $message = new SailthruMessage('tpl');
    $message->fromEmail('noreply@app.com');

    expect($message->getFromEmail())->toBe('noreply@app.com');
});

test('fromName method sets sender name', function () {
    $message = new SailthruMessage('tpl');
    $message->fromName('App');

    expect($message->getFromName())->toBe('App');
});

test('replyTo method sets reply-to address', function () {
    $message = new SailthruMessage('tpl');
    $message->replyTo('reply@app.com');

    expect($message->getReplyTo())->toBe('reply@app.com');
});

test('options method sets options array', function () {
    $message = new SailthruMessage('tpl');
    $message->replyTo('x@test.com');
    $message->options(['behalf_email' => 'sender@app.com']);

    expect($message->getOptions())->toHaveKey('behalf_email')
        ->and($message->getOptions()['behalf_email'])->toBe('sender@app.com');
});

test('fluent methods are chainable', function () {
    $message = (new SailthruMessage('tpl'))
        ->vars(['key' => 'value'])
        ->fromEmail('noreply@app.com')
        ->fromName('App')
        ->toEmail('user@test.com')
        ->replyTo('reply@app.com')
        ->options(['behalf_email' => 'sender@app.com']);

    expect($message)->toBeInstanceOf(SailthruMessage::class);
});

// ── Getter methods (TEST-03) ────────────────────────────────────────

test('constructor sets fromEmail and fromName from config', function () {
    config()->set('mail.from.address', 'configured@app.com');
    config()->set('mail.from.name', 'Configured App');

    $message = new SailthruMessage('tpl');

    expect($message->getFromEmail())->toBe('configured@app.com')
        ->and($message->getFromName())->toBe('Configured App');
});

test('static create method returns new instance', function () {
    $message = SailthruMessage::create('tpl');

    expect($message)->toBeInstanceOf(SailthruMessage::class)
        ->and($message->getTemplate())->toBe('tpl');
});

test('to method sets toEmail and toName from array', function () {
    $message = new SailthruMessage('tpl');
    $message->to(['email' => 'user@test.com', 'name' => 'User']);

    expect($message->getToEmail())->toBe('user@test.com')
        ->and($message->getToName())->toBe('User');
});

// ── mergeDefaultVars (TEST-04) ──────────────────────────────────────

test('mergeDefaultVars merges defaults under existing vars', function () {
    $message = new SailthruMessage('tpl');
    $message->vars(['existing' => 'yes']);
    $message->mergeDefaultVars(['existing' => 'no', 'default' => 'val']);

    expect($message->getVars())->toBe(['existing' => 'yes', 'default' => 'val']);
});

test('mergeDefaultVars with empty defaults preserves vars', function () {
    $message = new SailthruMessage('tpl');
    $message->vars(['key' => 'value']);
    $message->mergeDefaultVars([]);

    expect($message->getVars())->toBe(['key' => 'value']);
});

test('mergeDefaultVars with empty vars uses defaults', function () {
    $message = new SailthruMessage('tpl');
    $message->mergeDefaultVars(['key' => 'val']);

    expect($message->getVars())->toBe(['key' => 'val']);
});

// ── toEmails / isMultiSend (TEST-05) ────────────────────────────────

test('toEmails sets isMultiSend to true', function () {
    $message = new SailthruMessage('tpl');
    $message->toEmails(['a@test.com', 'b@test.com']);

    expect($message->isMultiSend())->toBeTrue();
});

test('toEmails joins emails with commas', function () {
    $message = new SailthruMessage('tpl');
    $message->toEmails(['a@test.com', 'b@test.com']);

    expect($message->getToEmail())->toBe('a@test.com,b@test.com');
});

test('isMultiSend is false by default', function () {
    $message = new SailthruMessage('tpl');

    expect($message->isMultiSend())->toBeFalse();
});
