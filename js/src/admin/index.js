import PermissionGrid from 'flarum/components/PermissionGrid';
import { extend } from 'flarum/extend';

app.initializers.add('fof-spamblock', () => {
    extend(PermissionGrid.prototype, 'moderateItems', items => {
        items.add('spamblockUsers', {
            icon: 'fas fa-pastafarianism',
            label: app.translator.trans('fof-spamblock.admin.permissions.spamblock_users_label'),
            permission: 'user.spamblock',
        });
    });
});
