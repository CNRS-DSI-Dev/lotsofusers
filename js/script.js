/**
 * ownCloud - lotsofusers
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2016 CNRS DSI
 */

(function ($, OC, OCA) {

    $(document).ready(function () {
        // user search
        var users = new OCA.LotsOfUsers.UserCollection;
        var usersView = new OCA.LotsOfUsers.UsersView({
            el: 'div.userSearch',
            collection: users
        });

        // group search for user creation
        var groups = new OCA.LotsOfUsers.GroupCollection;
        var groupsView = new OCA.LotsOfUsers.GroupsView({
            el: 'div#groupList',
            collection: groups
        });

        // user creation
        $('div.addUser button').on('click', function() {
            if ($('.addedUser').is(':visible')) {
                $('.addedUser').hide();
            }

            var username = $('#username').val();
            if ($.trim(username) === '') {
                OC.Notification.showTemporary(t('settings', 'Error creating user: {message}', {
                    message: t('settings', 'A valid username must be provided')
                }));
                return false;
            }

            var password = $('#password').val();
            if ($.trim(password) === '') {
                OC.Notification.showTemporary(t('settings', 'Error creating user: {message}', {
                    message: t('settings', 'A valid password must be provided')
                }));
                return false;
            }

            var groups = _.filter($('#addGroup').val().split(", "), function(group) { return group.trim() !== ''}) || [];

            $.post(
                OC.generateUrl('/settings/users/users'),
                {
                    username: username,
                    password: password,
                    groups: groups
                },
                function (result) {
                    $('.addedUser p').html(
                        t('lotsofusers', 'User successfully created: ')
                        + '<a href="' + OC.generateUrl('/lotsofusers/users/' + result.name) + '">'
                        + result.name
                        + '</a>'
                    );
                    $('.addedUser').show();
                }).fail(function(result) {
                    OC.Notification.showTemporary(t('settings', 'Error creating user: {message}', {
                        message: result.responseJSON.message
                    }, undefined, {escape: false}));
                });
        });
    });

})(jQuery, OC, OCA);
