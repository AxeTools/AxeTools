<?php

namespace App\Twig\Runtime;

use App\Utils\Gravatar;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface {
    public function __construct() {
        // Inject dependencies if needed
    }

    public static function getGravatarAvatar(string $email = 'ryanwhowe@gmail.com'): string {
        return Gravatar::create(40)->getAvatarUrl($email);
    }

    public static function getGravatarProfile(string $email = 'ryanwhowe@gmail.com'): string {
        return Gravatar::create()->getProfileUrl($email);
    }

    public static function wrapParen(string $string, string $left, string $right): string {
        $string = str_replace('(', $left.'(', $string);
        $string = str_replace(')', ')'.$right, $string);

        return $string;
    }
}
