<?php

namespace App\Notifications\Concerns;

trait ResolvesNotificationRoutes
{
    protected function resolveMailRoute(object $notifiable): string|array|null
    {
        if (method_exists($notifiable, 'routeNotificationFor')) {
            $route = $notifiable->routeNotificationFor('mail', $this);

            if (! empty($route)) {
                return $route;
            }
        }

        return data_get($notifiable, 'email');
    }

    protected function resolveSmsRoute(object $notifiable): ?string
    {
        if (method_exists($notifiable, 'routeNotificationFor')) {
            $route = $notifiable->routeNotificationFor('sms', $this);

            if (is_array($route)) {
                $route = reset($route) ?: null;
            }

            if (! empty($route)) {
                return (string) $route;
            }
        }

        $phone = data_get($notifiable, 'phone');

        return filled($phone) ? (string) $phone : null;
    }
}
