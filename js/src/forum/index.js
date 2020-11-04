import { extend } from 'flarum/extend';
import Button from 'flarum/components/Button';
import UserControls from 'flarum/utils/UserControls';

import User from 'flarum/models/User';
import Model from 'flarum/Model';

app.initializers.add('fof/spamblock', () => {
    User.prototype.canSpamblock = Model.attribute('canSpamblock');

    extend(UserControls, 'moderationControls', function(items, user) {
        if (user.canSpamblock()) {
            items.add(
                'spammer',
                Button.component({
                    icon: 'fas fa-pastafarianism',
                    onclick: () => {
                        if (!confirm(app.translator.trans('fof-spamblock.forum.user_controls.spammer_confirmation'))) return;

                        app.request({
                            url: `${app.forum.attribute('apiUrl')}/users/${user.id()}/spamblock`,
                            method: 'POST',
                        }).then(() => window.location.reload());
                    },
                }, app.translator.trans('fof-spamblock.forum.user_controls.spammer_button'))
            );
        }
    });
});
