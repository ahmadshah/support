<?php namespace Orchestra\Support;

use Illuminate\Support\Str as S;

class Str extends S
{
    /**
     * Convert slug type text to human readable text.
     *
     * @param  string   $text
     * @return string
     */
    public static function humanize($text)
    {
        return static::title(str_replace(array('-', '_'), ' ', $text));
    }

    /**
     * Replace a text using strtr().
     *
     * @param  string  $text
     * @param  array   $replacements
     * @return string
     */
    public static function replace($text, array $replacements = array())
    {
        $replacements = static::prepareBinding($replacements, '{', '}');

        $filter = function ($text) use ($replacements) {
            return strtr($text, $replacements);
        };

        if (is_array($text)) {
            $text = array_map($filter, $text);
        } else {
            $text = $filter($text);
        }

        return $text;
    }

    /**
     * Prepare bindings for text replacement.
     *
     * @param  array   $bindings
     * @param  string  $prefix
     * @param  string  $suffix
     */
    protected static function prepareBinding(array $bindings = array(), $prefix = '{', $suffix = '}')
    {
        foreach ($bindings as $key => $value) {
            $bindings["{$prefix}{$key}{$suffix}"] = $value;
        }

        return $bindings;
    }

    /**
     * Convert basic string to searchable result.
     *
     * @param  string   $text
     * @param  string   $wildcard
     * @param  string   $replacement
     * @return array
     */
    public static function searchable($text, $wildcard = '*', $replacement = '%')
    {
        if (! static::contains($text, $wildcard)) {
            return array(
                "{$text}",
                "{$text}{$replacement}",
                "{$replacement}{$text}",
                "{$replacement}{$text}{$replacement}",
            );
        }

        return array(str_replace($wildcard, $replacement, $text));
    }

    /**
     * Convert filter to string, this process is required to filter stream
     * data return from Postgres where blob type schema would actually use
     * BYTEA and convert the string to stream.
     *
     * @param  mixed    $data
     * @return string
     */
    public static function streamGetContents($data)
    {
        // check if it's actually a resource, we can directly convert
        // string without any issue.
        if (! is_resource($data)) {
            return $data;
        }

        // Get the content from stream.
        $hex = stream_get_contents($data);

        // For some reason hex would always start with 'x' and if we
        // don't filter out this char, it would mess up hex to string
        // conversion.
        if (preg_match('/^x(.*)$/', $hex, $matches)) {
            $hex = $matches[1];
        }

        // Check if it's actually a hex string before trying to convert.
        if (! ctype_xdigit($hex)) {
            return $hex;
        }

        return static::fromHex($hex);
    }

    /**
     * Convert hex to string.
     *
     * @param  string   $hex
     * @return string
     */
    protected static function fromHex($hex)
    {
        $data = '';

        // Convert hex to string.
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $data .= chr(hexdec($hex[$i].$hex[$i+1]));
        }

        return $data;
    }
}
