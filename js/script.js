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

    function delUser(username) {
        $.ajax({
            type: 'DELETE',
            url: OC.generateUrl('/settings/users/users'+'/{username}',{username: username}),
            async: false,
            success: function (result) {
                OC.redirect(OC.linkTo('lotsofusers', ''));
            },
            error: function (jqXHR) {
                OC.Notification.showTemporary(t('settings', 'Unable to delete {username}', {
                    username: username
                }, undefined, {escape: false}));
            }
        });
    }

    function changePassword(username, password) {
        $.ajax({
            type: 'POST',
            data: {username: username, password: password},
            url: OC.generateUrl('/settings/users/changepassword'),
            async: false,
            success: function (result) {
                $('#newpass').val('');
                $('#newpass').toggle();
                $('#validatepass').toggle();
                OC.Notification.showTemporary(t('lotsofusers', 'Password changed for user {username}', {
                    username: username
                }, undefined, {escape: false}));
            },
            error: function (jqXHR) {
                OC.Notification.showTemporary(t('lotsofusers', 'Unable to change password for {username}', {
                    username: username
                }, undefined, {escape: false}));
            }
        });
    }

    $(document).ready(function () {
        // ============= user search and create page
        if ($('#app-users').length) {
            // ============= user search
            var users = new OCA.LotsOfUsers.UserCollection;
            var usersView = new OCA.LotsOfUsers.UsersView({
                el: 'div.userSearch',
                collection: users
            });

            // ============= group search for user creation
            var groups = new OCA.LotsOfUsers.GroupCollection;
            var groupsView = new OCA.LotsOfUsers.GroupsView({
                el: 'div#groupList',
                collection: groups
            });

            // ============= user creation
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
                        $('#username').val('');
                        $('#password').val('');
                        $('#addGroup').val('');
                        $('.addedUser p').html(
                            t('lotsofusers', 'User successfully created: ')
                            + '<a href="' + OC.generateUrl('/apps/lotsofusers/users/' + result.name) + '">'
                            + result.name
                            + '</a>'
                        );
                        $('.addedUser').show();
                    }).fail(function(result) {
                        OC.Notification.showTemporary(t('settings', 'Error creating user: {message}', {
                            message: result.responseJSON.message
                        }, undefined, {escape: false}));
                    }
                );
            });
        }

        // ============= user details page
        if ($('#app-user').length) {

            // group completion
            var groups = new OCA.LotsOfUsers.GroupCollection;
            var groupsView = new OCA.LotsOfUsers.GroupsView({
                el: 'div#addGroup',
                collection: groups
            });
            groupsView.setMulti(false);

            // admin completion
            var admins = new OCA.LotsOfUsers.GroupCollection;
            var adminsView = new OCA.LotsOfUsers.GroupsView({
                el: 'div#addAdmin',
                collection: admins
            });
            adminsView.setMulti(false);

            // delete user
            $('#deluser').on('click', function() {
                var username =$('#username').val();
                OC.dialogs.confirm(
                    t('settings', 'Confirm suppression of {username} user ?', {username: username}),
                    t('settings', 'User suppression'),
                    function(okToSuppress) {
                        if (okToSuppress) {
                             delUser(username);
                        }
                    }
                );
            })

            // display change password form
            $('#changepass').on('click', function() {
                $('#newpass').toggle();
                $('#validatepass').toggle();
            });

            // change password
            $('#validatepass').on('click', function() {
                var username =$('#username').val();
                var password = $('#newpass').val();
                OC.dialogs.confirm(
                    t('lotsofusers', 'Confirm modification of {username} password ?', {username: username}),
                    t('lotsofusers', 'Password modification'),
                    function(okToChange) {
                        if (okToChange) {
                             changePassword(username, password);
                        }
                    }
                );
            });

            // set new quota
            $('#disk img').attr('title', t('lotsofusers', 'Please enter storage quota (ex: "512 MB" or "12 GB")'));

            $('#setquota').on('click', function() {
                var username = $('#username').val();
                var quota = $('#newquota').val();
                $.post(
                    OC.generateUrl('/settings/ajax/setquota.php'),
                    {
                        username: username,
                        quota: quota
                    },
                    function (result) {
                        if (result.status == 'success') {
                            $('#quotaValue').text(result.data.quota);
                            $('#newquota').val('');

                            var diskUsage = OC.Util.computerFileSize(($('#diskUsage span').text()));
                            var quota = OC.Util.computerFileSize(result.data.quota);
                            var ratio = (diskUsage * 100) / quota;
                            $('#quotabar > div').width('' + ratio + '%' );
                        }
                    }).fail(function(result) {
                        OC.Notification.showTemporary(t('lotsofusers', 'Error setting quota: {message}', {
                            message: result.responseJSON.data.message
                        }, undefined, {escape: false}));
                    }
                );
            });

            // detach from group
            $('#groups').on('click', '.user img.action', function() {
                var img = $(this);
                $.post(
                    OC.generateUrl('/settings/ajax/togglegroups.php'),
                    {
                        username: img.data('uid'),
                        group: img.data('gid')
                    },
                    function (result) {
                        if (result.status == 'success') {
                            img.parent().remove();
                        }
                        else {
                            OC.Notification.showTemporary(t('lotsofusers', 'Error removing from group: {message}', {
                                message: result.data.message
                            }, undefined, {escape: false}));
                        }
                    }).fail(function(result) {
                        OC.Notification.showTemporary(t('lotsofusers', 'Error removing from group: {message}', {
                            message: result.responseJSON.data.message
                        }, undefined, {escape: false}));
                    }
                );
            });

            // detach from admin
            $('#groups').on('click', '.admin img', function() {
                var img = $(this);
                $.post(
                    OC.generateUrl('/settings/ajax/togglesubadmins.php'),
                    {
                        username: img.data('uid'),
                        group: img.data('gid')
                    },
                    function (result) {
                        if (result.status == 'success') {
                            img.parent().remove();
                        }
                        else {
                            OC.Notification.showTemporary(t('lotsofusers', 'Error removing from admin group: {message}', {
                                message: result.data.message
                            }, undefined, {escape: false}));
                        }
                    }).fail(function(result) {
                        OC.Notification.showTemporary(t('lotsofusers', 'Error removing from admin group: {message}', {
                            message: result.responseJSON.data.message
                        }, undefined, {escape: false}));
                    }
                );
            });

            // add in group
            $('#addInGroup').on('click', function() {
                var uid = $(this).data('uid');
                var gid = $('#newgroup').val();
                $.post(
                    OC.generateUrl('/settings/ajax/togglegroups.php'),
                    {
                        username: uid,
                        group: gid
                    },
                    function (result) {
                        if (result.status == 'success') {
                            var div = $('<div>');

                            var span = $('<span>');
                            span.text(result.data.groupname);
                            div.append(span);

                            var img=$('<img>')
                                .attr('data-gid', result.data.groupname)
                                .attr('data-uid', result.data.username)
                                .attr('src', OC.imagePath('core', 'actions/delete.svg'));
                            div.append(img);

                            $('#groups .user div.groupList').append(div);

                            $('#newgroup').val('');
                        }
                        else {
                            OC.Notification.showTemporary(t('lotsofusers', 'Error adding in this group: {message}', {
                                message: result.responseJSON.data.message
                            }, undefined, {escape: false}));
                        }
                    }).fail(function(result) {
                        OC.Notification.showTemporary(t('lotsofusers', 'Error adding in this group: {message}', {
                            message: result.responseJSON.data.message
                        }, undefined, {escape: false}));
                    }
                );
            });

            // add in admin
            $('#setAdmin').on('click', function() {
                var uid = $(this).data('uid');
                var gid = $('#newAdmin').val();
                $.post(
                    OC.generateUrl('/settings/ajax/togglesubadmins.php'),
                    {
                        username: uid,
                        group: gid
                    },
                    function (result) {
                        if (result.status == 'success') {
                            var div = $('<div>');

                            var span = $('<span>');
                            span.text(gid);
                            div.append(span);

                            var img=$('<img>')
                                .attr('data-gid', gid)
                                .attr('data-uid', uid)
                                .attr('src', OC.imagePath('core', 'actions/delete.svg'));
                            div.append(img);

                            $('#groups .admin div.adminList').append(div);

                            $('#newAdmin').val('');
                        }
                        else {
                            OC.Notification.showTemporary(t('lotsofusers', 'Error adding in this group: {message}', {
                                message: result.responseJSON.data.message
                            }, undefined, {escape: false}));
                        }
                    }).fail(function(result) {
                        OC.Notification.showTemporary(t('lotsofusers', 'Error adding admin in this group: {message}', {
                            message: result.responseJSON.data.message
                        }, undefined, {escape: false}));
                    }
                );
            });

            // restore requests list
            var uid = $('#username').val();
            $.get(
                OC.generateUrl('/apps/user_files_restore/api/1.0/requests' + '?uid=' + uid),
                function(data) {
                    if (data.status === 'success') {
                        var $ul = $('<ul>');
                        _.each(data.data.requests, function(elt) {
                            var $path = $('<strong>' + elt.path + '</strong>');
                            var text = ' (' + t('lotsofusers', "d - ") + elt.version + ', ' + elt.status + ') ';
                            var $span = $('<span>').text(elt.date);
                            var $li = $('<li>');
                            $li.text(text);
                            $li.prepend($path);
                            $li.append($span);

                            $ul.append($li);
                        });

                        $('#restore > p').after($ul);
                    }

                }
            );

            // migration requests list
            var uid = $('#username').val();
            $.get(
                OC.generateUrl('/apps/user_files_migrate/api/1.0/requests' + '?uid=' + uid),
                function(data) {
                    if (data.status === 'success') {
                        var $ul = $('<ul>');
                        _.each(data.data.requests, function(elt) {
                            if (elt.requester !== "[ user ]") {
                                var $requester = $('<strong>').text(elt.requester);
                                var $recipient = ' => ' + elt.recipient + ' (' + elt.status + ') ';
                                var $text = '';
                                // var text = elt.requester + ' => <strong>' + elt.recipient + '</strong> (' + elt.status + ') ';
                            }
                            else {
                                var $requester = elt.requester + ' => ';
                                var $recipient = $('<strong>').text(elt.recipient);
                                var $text = ' (' + elt.status + ') ';
                            }

                            var $span = $('<span>').text(elt.date);

                            var $li = $('<li>');
                            $li.append($requester);
                            $li.append($recipient);
                            $li.append($text);
                            $li.append($span);

                            $ul.append($li);
                        });

                        $('#migrate > p').after($ul);
                    }

                }
            );
        }

        // ============= groups search and create page
        if ($('#app-groups').length) {
            // ============= group search
            var groups = new OCA.LotsOfUsers.GroupCollection;
            var groupsView = new OCA.LotsOfUsers.GroupsView({
                el: 'div.groupSearch',
                collection: groups
            });
            groupsView.setMulti(false);
            groupsView.clickUrl = function(group) {
                OC.redirect(OC.generateUrl('/apps/lotsofusers/groups/' + group));
            }
            groupsView.groupsViewTemplate =
                '{{#if groups}}' +
                '    {{#each groups}}' +
                '    <li><span class="group">{{gid}}</span> <span class="info">({{usersCount}} users)</span></li>' +
                '    {{/each}}' +
                '{{/if}}';

            // ============= create group
            $('div.addGroup button').on('click', function() {
                if ($('.addedGroup').is(':visible')) {
                    $('.addedGroup').hide();
                }

                var groupname = $('#groupname').val();
                if ($.trim(groupname) === '') {
                    OC.Notification.showTemporary(t('settings', 'Error creating group: {message}', {
                        message: t('settings', 'A valid groupname must be provided')
                    }));
                    return false;
                }

                $.post(
                    OC.generateUrl('/settings/users/groups'),
                    {
                        id: groupname
                    },
                    function (result) {
                        $('#groupname').val('');
                        $('.addedGroup p').html(
                            t('lotsofusers', 'Group successfully created: ')
                            + '<a href="' + OC.generateUrl('/apps/lotsofusers/groups/' + result.groupname) + '">'
                            + result.groupname
                            + '</a>'
                        );
                        $('.addedGroup').show();
                    }).fail(function(result) {
                        OC.Notification.showTemporary(t('settings', 'Error creating group: {message}', {
                            message: result.responseJSON.message
                        }, undefined, {escape: false}));
                    }
                );
            });
        }

        // ============= group details page
        if ($('#app-group').length) {
            var tableUsers = $('#tableUsers').DataTable({
                stateSave: true,
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "render": function(data, type, row) {
                            return '<a href="' + OC.generateUrl('/apps/lotsofusers/users/' + data) + '">' + data + '</a>';
                        }
                    },
                    {
                        "targets": [ 2 ],
                        "visible": false,
                        "searchable": false
                    },
                    {
                        "targets": -1,
                        className: "diskUsage",
                        // "data": null,
                        // "defaultContent": "<button>Calculate</button>"
                        "render": function(data) {
                            // if (data == undefined || data.trim() == '') {
                                if (!data) {
                                return "";
                            }
                            return data;
                        }
                    }
                ],
            });

            // display diskUsage of a user
            $('#tableUsers tbody').on('click', 'td.diskUsage', function() {
                var cell = tableUsers.cell(this);
                var data = tableUsers.row($(this).parents('tr')).data();

                $.ajax({
                    url: OC.generateUrl('/apps/lotsofusers/api/v1/diskusage/' + data[0]),
                    success: function(result) {
                        if (!result) {
                            result = "No space used";
                        }
                        else {
                            result = OC.Util.humanFileSize(result);
                        }
                        cell.data(result).draw();//.invalidate('data')
                    }
                });
            });

            // visibility of columns
            $('a.toggle-vis').on('click', function (e) {
                e.preventDefault();

                // Get the column API object
                var column = tableUsers.column($(this).attr('data-column'));

                // Toggle the visibility
                column.visible(! column.visible());
            });
        }
    });

})(jQuery, OC, OCA);
