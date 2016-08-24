<?php
namespace App\Aws;

use Aws\Credentials\CredentialProvider;
use Symfony\Component\Yaml\Yaml;

class CredentialsProvider
{
    public function getProvider()
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../../../config/config.yml'));
        $profile = $config['aws']['profile'];
        $path = __DIR__ . '/../../../config/.aws-credentials';
        $provider = CredentialProvider::ini($profile, $path);
        $provider = CredentialProvider::memoize($provider);
        return $provider;
    }
}