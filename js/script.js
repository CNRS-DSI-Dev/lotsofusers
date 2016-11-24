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
        var users = new OCA.LotsOfUsers.UserCollection;
        var usersView = new OCA.LotsOfUsers.UsersView({
            el: 'div.userSearch',
            collection: users
        });

        var groups = new OCA.LotsOfUsers.GroupCollection;
        var groupsView = new OCA.LotsOfUsers.GroupsView({
            el: 'div#groupList',
            collection: groups
        });
    });

})(jQuery, OC, OCA);
