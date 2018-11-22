import { extend } from 'flarum/extend';
import Button from 'flarum/components/Button';
import UserControls from 'flarum/utils/UserControls';

app.initializers.add('fof/spamblock', () => {
    extend(UserControls, 'moderationControls', function(items, user) {
        items.add(
            'spammer',
            Button.component({
                icon: 'fas fa-pastafarianism',
                children: app.translator.trans('fof-spammer.forum.user_controls.spammer_button'),
                onclick: () => {
                    if (!confirm(app.translator.trans('fof-spammer.forum.user_controls.spammer_confirmation'))) return;

                    app.request({
                        url: `${app.forum.attribute('apiUrl')}/users/${user.id()}/spamblock`,
                        method: 'POST',
                    }).then(() => window.location.reload());
                },
            })
        );
    });
});
