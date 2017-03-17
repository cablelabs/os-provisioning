<?php namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'event.name' => [
			'EventListener',
		],
	];

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);

		\Event::listen(['eloquent.created: *', 'eloquent.updated: *', 'eloquent.deleted: *', 'eloquent.restored: *'], function ($object) {
			$json = json_encode([
				'id' => $object->id,
				'table' => $object->table,
				'event' => explode('.', explode(':', \Event::firing())[0])[1],
				'diff' => json_encode(array_diff_assoc($object->getAttributes(), $object->getOriginal())),
			]);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1');
			curl_setopt($ch, CURLOPT_PORT , 8008);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 1000);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json', 'Expect:']);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_exec($ch);
			curl_close($ch);
		});
	}

}
