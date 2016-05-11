<?php
// Routes

$app->get('/', App\Action\IndexGet::class)
    ->setName('homepage')->add($authorize);

$app->get('/login', App\Action\LoginGet::class)
    ->setName('login');

$app->post('/login', App\Action\LoginPost::class)
    ->setName('loginHandler');

$app->get('/logout', App\Action\LogoutGet::class)
    ->setName('logoutHandler');

$app->get('/new', App\Action\NewGet::class)
    ->setName('newFormHandler')
    ->add($authorize);

$app->get('/record/{record}', App\Action\RecordGet::class)
    ->setName('recordHandler')
    ->add($authorize);

$app->get('/fields', App\Action\FieldsGet::class)
    ->setName('fieldsHandler')
    ->add($authorize);