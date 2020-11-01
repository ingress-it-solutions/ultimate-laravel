<?php


namespace Ultimate\Laravel;


use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\ArgvInput;

class Filters
{
    /**
     * Determine if the given request should be monitored.
     *
     * @param string[] $notAllowed
     * @param Request $request
     * @return bool
     */
    public static function isApprovedRequest(array $notAllowed, Request $request): bool
    {
        foreach ($notAllowed as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if current command should be monitored.
     *
     * @param string[] $notAllowed
     * @return bool
     */
    public static function isApprovedArtisanCommand(array $notAllowed = null): bool
    {
        $input = new ArgvInput();

        return is_null($notAllowed)
            ? true
            : !in_array($input->getFirstArgument(), $notAllowed);
    }

    /**
     * Determine if the given Job class should be monitored.
     *
     * @param null|string[] $notAllowed
     * @param string $class
     * @return bool
     */
    public static function isApprovedJobClass(string $class, array $notAllowed = null)
    {
        return is_array($notAllowed) ? !in_array($class, $notAllowed) : true;
    }

    /**
     * Hide the given request parameters.
     *
     * @param array $data
     * @param array $hidden
     * @return array
     */
    public static function hideParameters($data, $hidden)
    {
        foreach ($hidden as $parameter) {
            if (Arr::get($data, $parameter)) {
                Arr::set($data, $parameter, '********');
            }
        }

        return $data;
    }
}
