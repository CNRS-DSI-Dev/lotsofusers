(function(OC, OCA) {
    var SEARCH_USERS_TPL =
        '   <div class="detailFileInfoContainer">' +
        '<div class="list">' +
        '   {{#if users}}' +
        '    <ul>' +
        '       {{#each users}}' +
        '       <li><span class="login">{{login}}</span> <span class="groups">{{groups}}</span></li>' +
        '       {{/each}}' +
        '   </ul>' +
        '   {{/if}}' +
        '</div>';

    // ******************************** User model
    var UserModel = OC.Backbone.Model.extend({
        getGroupsString: function() {
            return this.get('groups').reduce(function(result, group){
                return result + ", " + group;
            })
        }

        // initialize: function(){
        //     console.log("Initialize UserModel!");
        // }

    });
    OCA.LotsOfUsers.UserModel = UserModel;

    // ******************************** User collection
    var UserCollection = OC.Backbone.Collection.extend({
        model: OCA.LotsOfUsers.UserModel,

        url: '/apps/lotsofusers/api/v1/users',

        parse: function(response) {
            return response.users;
        },

        // initialize: function(){
        //     console.log("Initialize userCollection!");
        // },

        render: function() {
            this.el.innerHTML = this.model.get('login');
        }
    });
    OCA.LotsOfUsers.UserCollection = UserCollection;

    // ******************************** User view
    var UserView = OC.Backbone.View.extend({
        tagName: "li",
        className: "test",
        // model: OCA.LotsOfUsers.UserModel,

        render: function() {
            this.$el.text(this.model.get('login') + " " + this.model.get('groups').reduce(function(result, group){
                return result + group + ", ";
            }));
            return this;
        }
    });
    OCA.LotsOfUsers.UserView = UserView;

    // ******************************** List view of users
    var UsersView = OC.Backbone.View.extend({
        el: "ul#test",
        // collection: OCA.LotsOfUsers.UserCollection,

        _template: null,

        initialize: function() {
            this.listenTo(this.collection, 'sync', this.render);

            this.collection.fetch();
        },

        template: function(vars) {
            if (!this._template) {
                this._template = Handlebars.compile(SEARCH_USERS_TPL);
            }
            return this._template(vars);
        },

        render: function() {
            if (this.collection.length) {
                var params = {};
                params.users = [];
                this.collection.each(function(user) {
                    // var userView = new OCA.LotsOfUsers.UserView({model: user});
                    // $(this.el).append(userView.render().el);

                    params.users.push({
                        'login': user.get('login'),
                        'groups': user.getGroupsString()
                    });

                }, this);

                this.$el.html(this.template(params));
            }
        }
    });
    OCA.LotsOfUsers.UsersView = UsersView;
})(OC, OCA);
