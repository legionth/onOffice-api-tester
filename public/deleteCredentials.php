<?php

namespace jr\ooapi;

include(__DIR__.'/../vendor/autoload.php');

$config = new Config();
$credentialStorage = new CredentialStorage();
$credentialStorage->delete($config->getCredentialDir());
