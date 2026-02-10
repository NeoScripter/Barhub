<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $starts_at
 * @property string $ends_at
 * @property string $location
 * @property string $buildin_folder_url
 * @property int $is_active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\ExhibitionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition whereBuildinFolderUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exhibition whereUpdatedAt($value)
 */
	final class Exhibition extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \App\Enums\UserRole $role
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property int $status
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exhibition> $exhibitions
 * @property-read int|null $exhibitions_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	final class User extends \Eloquent {}
}

