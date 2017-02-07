/* global Handlebars, escapeHTML */

(function(OC, OCA) {

    var SEARCH_USERS_TPL =
        '   {{#if users}}' +
        '       {{#each users}}' +
        // '       <li><a href="' + OC.generateUrl('/apps/lotsofusers/users/') + '{{login}}"><span class="login">{{login}}</span> <span class="groups">{{groups}}</span></a></li>' +
        '       <li><span class="login">{{login}}</span> <span class="groups">{{groups}}</span></li>' +
        '       {{/each}}' +
        '   {{/if}}';

    // ******************************** List view of users
    var UsersView = OC.Backbone.View.extend({
        // el: "div.userSearch",
        // collection: OCA.LotsOfUsers.UserCollection,
        // multi = false

        _$field: null,
        _$resultsList: null,
        multi: false,
        clickUrl: false, // or callback function
        _$more: null,
        _template: null,

        events: {
            'paste input[type=text]' : 'showResults',
            'input input[type=text]' : 'showResults',
            'blur input'             : '_hideResults',
            // 'blur ul'                : '_hideResults',
            'keydown input'          : 'keyDown',
            'mousedown li'           : 'clickSelect'
        },

        initialize: function() {
            this._$field = this.$el.find('input[type=text]');
            this._$resultsList = this.$el.find('ul.list');
            this._$more = this.$el.find('.more');

            this.listenTo(this.collection, 'sync', this.render);

            // this.collection.fetch({reset: true});
        },

        setMulti: function(multi) {
            this.multi = multi;
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

        _hideResults: function(e) {
            this._$resultsList.hide();
            this._$resultsList.html('');
            this._$more.hide();
            this._$more.html('');
        },

        _selectResult: function($target) {
            // clear 'selected' classes, then add it back to the desired result
            this._$resultsList.children().removeClass('selected');
            $target.addClass('selected');
        },

        _replace: function(text) {
            if (this.multi) {
                var before = this._$field.val().match(/^.+,\s*|/)[0];
                this._$field.val(before + text + ", ");
            }
            else {
                this._$field.val(text);
            }
        },

        clickSelect: function(e) {
            var $elt = $(e.target).parent('li').find('span.login');

            if (!this.clickUrl) {
                if(this._$resultsList.is(':visible')) {
                    this._selectResult($elt);
                    this._replace($elt.text());
                    this._hideResults();
                }
            }
            else {
                this.clickUrl($elt.text());
                this._hideResults();
            }
        },

        keyDown: function(e) {
            // If the dropdown `ul` is in view, then act on keydown for the following keys:
            // Enter / Esc / Up / Down
            if(this._$resultsList.is(':visible')) {
                var $items = this._$resultsList.children(),
                    $selected = $items.filter('.selected');

                if (e.which === 13) { // Enter
                    e.preventDefault();
                    if ($selected.length != 0) {
                        if (!this.clickUrl) {
                            this._replace($selected.find('span.login').text());
                            this._hideResults();
                        }
                        else {
                            this._hideResults();
                            this.clickUrl($selected.find('span.login').text());
                        }
                    }
                }
                else if (e.which === 27) { // Esc
                    this._hideResults();
                }
                else if (e.which === 38 || e.which === 40) { // Up/Down arrow
                    $target = null;
                    if (e.which === 38) {
                        if ($selected.is(':first-child') || $selected.length == 0) {
                            $target = $items.filter(':last-child');
                        }
                        else {
                            $target = $selected.prev('li');
                        }
                    }
                    else {
                        if ($selected.is(':last-child') || $selected.length == 0) {
                            $target = $items.filter(':first-child');
                        }
                        else {
                            $target = $selected.next('li');
                        }
                    }

                    this._selectResult($target);
                }
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
                this._$resultsList.show();

                if (this.collection.length < this.collection.totalCount) {
                    this._$more.html(t('lotsofusers', '{nb} more users...', {nb: this.collection.totalCount - this.collection.length})).show();
                    this._$more.show();
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

