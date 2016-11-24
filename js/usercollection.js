(function(OC, OCA) {

    // ******************************** User collection
    var UserCollection = OC.Backbone.Collection.extend({
        model: OCA.LotsOfUsers.UserModel,
        search: '',
        totalCount: 0,

        url: function() {
            return '/apps/lotsofusers/api/v1/users/' + this.search;
        },

        parse: function(response) {
            this.totalCount = parseInt(response.totalCount);
            return response.users;
        },

        // initialize: function(){
        //     console.log("Initialize userCollection!");
        // },

        comparator: 'login',

        render: function() {
            this.el.innerHTML = this.model.get('login');
        }
    });
    OCA.LotsOfUsers.UserCollection = UserCollection;

})(OC, OCA);

