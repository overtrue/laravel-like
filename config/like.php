<?php

return [
    /**
     * Use uuid as primary key.
     */
    'uuids' => false,

    /*
     * User tables foreign key name.
     */
    'user_foreign_key' => 'user_id',

    /*
     * Table name for likes records.
     */
    'likes_table' => 'likes',

    /*
     * Model name for like record.
     */
    'like_model' => \Overtrue\LaravelLike\Like::class,

    /*
     * Model name for liker.
     */
    'user_model' => class_exists(\App\Models\User::class) ? \App\Models\User::class : null,
];
