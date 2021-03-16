import app from 'flarum/common/app';

app.initializers.add('fof-spamblock', () => {
    app.extensionData.for('fof-spamblock')
        .registerPermission({
            icon: 'fas fa-pastafarianism',
            label: app.translator.trans('fof-spamblock.admin.permissions.spamblock_users_label'),
            permission: 'user.spamblock',
        }, 'moderate');
});
