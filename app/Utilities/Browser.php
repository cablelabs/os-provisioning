<?php

namespace App\Utilities;

use HeadlessChromium\Browser as HeadlessChromiumBrowser;
use HeadlessChromium\Browser\ProcessAwareBrowser;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use Illuminate\Support\Facades\Cache;

class Browser
{
    protected static function options(): array
    {
        return [
            'keepAlive' => true,
            'debugLogger' => null,
            'noSandbox' => false,
        ];
    }

    public static function get(array $options = []): HeadlessChromiumBrowser|ProcessAwareBrowser
    {
        /**
         * Use Atomic locks to avoid race condition when
         * more than one job runs at the same time
         * and requests a browser instance
         */
        $lock = Cache::lock('resolve-browser-instance', 15);
        $browserResolved = false;

        do {
            if (! $lock->get()) {
                sleep(1);
                continue;
            }

            $browser = static::existingOrNewBrowser($options);
            $browserResolved = true;
            $lock->forceRelease();
        } while (! $browserResolved);

        return $browser;
    }

    protected static function existingOrNewBrowser(array $options = []): HeadlessChromiumBrowser|ProcessAwareBrowser
    {
        $options = array_merge(static::options(), $options);
        $socketFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.'chrome-php-chromium-browser.sock';
        $socket = rescue(fn () => file_get_contents($socketFile), null, false);

        try {
            // socket file might be empty or doesn't exist (on first run)
            throw_unless($socket, BrowserConnectionFailed::class, 'Empty Socket');
            $browser = BrowserFactory::connectToBrowser($socket, $options);
        } catch (BrowserConnectionFailed $e) {
            // The browser was probably closed, start it again
            $browser = (new BrowserFactory('chromium-browser'))->createBrowser($options);
            // save the uri to be able to connect again to browser
            file_put_contents($socketFile, $browser->getSocketUri(), LOCK_EX);
        }

        return $browser;
    }
}
