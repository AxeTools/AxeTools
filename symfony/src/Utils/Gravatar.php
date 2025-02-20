<?php

namespace App\Utils;

class Gravatar {
    public const MAX_RATING_G = 'g';   // suitable for display on all websites with any audience type
    public const MAX_RATING_PG = 'pg'; // may contain rude gestures, provocatively dressed individuals, the lesser swear words, or mild violence
    public const MAX_RATING_R = 'r';   // may contain such things as harsh profanity, intense violence, nudity, or hard drug use
    public const MAX_RATING_X = 'x';   // may contain hardcore sexual imagery or extremely disturbing violence

    public const IMAGE_SIZE_MAX = 2048;

    public const IMAGE_EXTENSION_JPG = 'jpg';
    public const IMAGE_EXTENSION_JPEG = 'jpeg';
    public const IMAGE_EXTENSION_GIF = 'gif';
    public const IMAGE_EXTENSION_PNG = 'png';

    public const URL_AVATAR_HTTP = 'http://www.gravatar.com/avatar/';
    public const URL_AVATAR_HTTPS = 'https://www.gravatar.com/avatar/';

    public const URL_PROFILE_HTTP = 'http://www.gravatar.com/';
    public const URL_PROFILE_HTTPS = 'https://www.gravatar.com/';

    public const IMAGE_DEFAULT_404 = '404';
    public const IMAGE_DEFAULT_MYSTERY = 'mp';
    public const IMAGE_DEFAULT_IDENTICON = 'identicon';
    public const IMAGE_DEFAULT_MONSTERID = 'monsterid';
    public const IMAGE_DEFAULT_WAVATAR = 'wavatar';
    public const IMAGE_DEFAULT_RETRO = 'retro';
    public const IMAGE_DEFAULT_ROBOHASH = 'robohash';
    public const IMAGE_DEFAULT_BLANK = 'blank';

    /**
     * @var int - The size to use for avatars
     */
    protected int $size = 80;

    /**
     * @var string|null - The default image to use - either a string of the gravatar-recognized default image
     */
    protected mixed $default_image = null;

    /**
     * @var string - The maximum rating to allow for the avatar
     */
    protected string $max_rating = self::MAX_RATING_G;

    /**
     * @var bool - Should we use the secure (HTTPS) URL base?
     */
    protected bool $use_secure_url = true;

    public static function create(int $size = 80, bool $secure = true, string $max_rating = self::MAX_RATING_G, ?string $default = null): Gravatar {
        $self = new self();
        $self->use_secure_url = $secure;
        $self->setAvatarSize($size)
            ->setDefaultImage($default)
            ->setMaxRating($max_rating);

        return $self;
    }

    /**
     * Get the currently set avatar size.
     *
     * @return int - The current avatar size in use
     */
    public function getAvatarSize(): int {
        return $this->size;
    }

    /**
     * Set the avatar size to use.
     *
     * @param int $size - The avatar size to use, must be less than 2024 and greater than 0
     *
     * @throws \InvalidArgumentException
     */
    private function setAvatarSize(int $size): static {
        $this->size = $size;

        if ($this->size > self::IMAGE_SIZE_MAX || $this->size < 0) {
            throw new \InvalidArgumentException('Avatar size must be within 0 pixels and '.self::IMAGE_SIZE_MAX.' pixels');
        }

        return $this;
    }

    /**
     * Set the default image to use for avatars.
     *
     * @param mixed $image - The default image to use. Use null for the gravatar default, a string containing
     *                     a valid image URL, or a string specifying a recognized gravatar "default".
     *
     * @throws \InvalidArgumentException
     */
    public function setDefaultImage(mixed $image): static {
        if (null === $image) {
            return $this;
        }

        // Check $image against recognized gravatar "defaults", and if it doesn't match any of those we need to see if it is a valid URL.
        $gravatar_default_name_check = strtolower(trim($image));
        $valid_defaults = [
            self::IMAGE_DEFAULT_404,
            self::IMAGE_DEFAULT_MYSTERY,
            self::IMAGE_DEFAULT_IDENTICON,
            self::IMAGE_DEFAULT_MONSTERID,
            self::IMAGE_DEFAULT_WAVATAR,
            self::IMAGE_DEFAULT_RETRO,
            self::IMAGE_DEFAULT_ROBOHASH,
            self::IMAGE_DEFAULT_BLANK,
        ];
        if (in_array($gravatar_default_name_check, $valid_defaults)) {
            $this->default_image = $gravatar_default_name_check;
        } else {
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                $allowed_extensions = [
                    self::IMAGE_EXTENSION_JPG,
                    self::IMAGE_EXTENSION_JPEG,
                    self::IMAGE_EXTENSION_GIF,
                    self::IMAGE_EXTENSION_PNG,
                ];
                $extension = pathinfo($image, PATHINFO_EXTENSION);
                if (!in_array($extension, $allowed_extensions)) {
                    throw new \InvalidArgumentException("image extension '".$extension."' is not allowed by gravatar, must be of type '".implode("','", $allowed_extensions)."'");
                }
                $this->default_image = rawurlencode($image);
            } else {
                throw new \InvalidArgumentException('The default image specified is not a recognized gravatar "default" and is not a valid URL');
            }
        }

        return $this;
    }

    /**
     * Set the maximum allowed rating for avatars.
     *
     * @param string $rating - The maximum rating to use for avatars\
     *
     * @throws \InvalidArgumentException
     */
    public function setMaxRating(string $rating): static {
        $rating = strtolower($rating);
        $valid_ratings = [
            self::MAX_RATING_G,
            self::MAX_RATING_PG,
            self::MAX_RATING_R,
            self::MAX_RATING_X,
        ];
        if (!in_array($rating, $valid_ratings)) {
            throw new \InvalidArgumentException(sprintf('Invalid rating "%s" specified, only "'.implode(',', $valid_ratings).'" are allowed to be used.', $rating));
        }

        $this->max_rating = $rating;

        return $this;
    }

    /**
     * Build the avatar URL based on the provided email address.
     *
     * @param string $email - The email to get the gravatar for
     *
     * @return string - The safe URL to the gravatar
     */
    public function getAvatarUrl(string $email): string {
        // Start building the URL, and deciding if we're doing this via HTTPS or HTTP.
        if ($this->use_secure_url) {
            $url = static::URL_AVATAR_HTTPS;
        } else {
            $url = static::URL_AVATAR_HTTP;
        }

        // Tack the email hash onto the end.
        if (!empty($email)) {
            $url .= self::getEmailHash($email);
        } else {
            $url .= str_repeat('0', 32);
        }

        // Time to figure out our request params
        $params = [
            's' => $this->size,
            'r' => $this->max_rating,
        ];
        if (null !== $this->default_image) {
            $params['d'] = $this->default_image;
        }

        // Handle "null" gravatar requests.
        if (empty($email)) {
            $params['f'] = 'y';
        }

        // And we're done.
        return $url.'?'.http_build_query($params);
    }

    public function getProfileUrl(string $email): string {
        // Start building the URL, and deciding if we're doing this via HTTPS or HTTP.
        if ($this->use_secure_url) {
            $url = static::URL_PROFILE_HTTPS;
        } else {
            $url = static::URL_PROFILE_HTTP;
        }

        // Tack the email hash onto the end.
        if (!empty($email)) {
            $url .= self::getEmailHash($email);
        } else {
            $url .= str_repeat('0', 32);
        }

        // And we're done.
        return $url;
    }

    /**
     * Generate the md5 hash of the email address, this is how gravatar requests are formatted.
     */
    private static function getEmailHash(string $email): string {
        // Using md5 as per gravatar docs.
        return hash('sha256', strtolower(trim($email)));
    }
}
