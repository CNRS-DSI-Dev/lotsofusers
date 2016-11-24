(function(OC, OCA) {

    // ******************************** User collection
    var GroupCollection = OC.Backbone.Collection.extend({
        model: OCA.LotsOfUsers.GroupModel,
        search: '',
        totalCount: 0,

        url: function() {
            return '/apps/lotsofusers/api/v1/groups/' + this.search;
        },

        parse: function(response) {
            this.totalCount = parseInt(response.totalCount);
            return response.groups;
        },

        comparator: 'gid',

        render: function() {
            this.el.innerHTML = this.model.get('gid');
        }
    });
    OCA.LotsOfUsers.GroupCollection = GroupCollection;

})(OC, OCA);

