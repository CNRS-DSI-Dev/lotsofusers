/* global Handlebars, escapeHTML */

(function(OC, OCA) {

    var SEARCH_USERS_TPL =
        '   {{#if users}}' +
        '       {{#each users}}' +
        '       <li><span class="login"><a href="{{login}}">{{login}}</a></span> <span class="groups">{{groups}}</span></li>' +
        '       {{/each}}' +
        '   {{/if}}';

    // ******************************** List view of users
    var UsersView = OC.Backbone.View.extend({
        // el: "div.userSearch",
        // collection: OCA.LotsOfUsers.UserCollection,

        _$field: null,
        _$resultsList: null,
        _$more: null,
        _template: null,

        events: {
            'paste input[type=text]' : 'showResults',
            'input input[type=text]' : 'showResults'
            // 'keyup input[type=text]' : 'showResults'
        },

        initialize: function() {
            this._$field = this.$el.find('input[type=text]');
            this._$resultsList = this.$el.find('ul.list');
            this._$more = this.$el.find('.more');

            this.listenTo(this.collection, 'sync', this.render);

            // this.collection.fetch({reset: true});
        },

        showResults: function(e) {
            if (this._$field.val().length <= 1) {
                this._$more.hide();
                this._$resultsList.html('');
            }
            else {
                if (this.timeoutId != false) {
                    clearTimeout(this.timeoutId);
                    this.timeoutId = false;
                }

                var self = this;
                this.timeoutId = setTimeout(function() {
                    self.collection.search = self._$field.val();
                    self.collection.fetch({reset: true});
                }
                , 400);
            }
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

                this._$resultsList.html(this.template(params));

                if (this.collection.length < this.collection.totalCount) {
                    this._$more.html(t('lotsofusers', '{nb} more users...', {nb: this.collection.totalCount - this.collection.length})).show();
                }
                else {
                    this._$more.hide();
                }
            }
            else {
                this._$more.hide();
                this._$resultsList.html('');
            }
        }
    });
    OCA.LotsOfUsers.UsersView = UsersView;

})(OC, OCA);

