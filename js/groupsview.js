/* global Handlebars, escapeHTML */

(function(OC, OCA) {

    var SEARCH_GROUPS_TPL =
        '{{#if groups}}' +
        '    {{#each groups}}' +
        '    <li><span class="group">{{gid}}</span></li>' +
        '    {{/each}}' +
        '{{/if}}';

    // ******************************** List view of users
    var GroupsView = OC.Backbone.View.extend({
        // el: "div#groupList",
        // collection: OCA.LotsOfUsers.GroupCollection,
        // multi: true,

        _$field: null,
        _$resultsList: null,
        multi: true,

        events: {
            'paste input'   : 'showResults',
            'input input'   : 'showResults',
            // 'blur input'    : '_hideResults',
            // 'blur ul'       : '_hideResults',
            'mousedown li'  : 'clickSelect',
            'keydown input' : 'keyDown'
            // 'keyup input[type=text]' : 'showResults'
        },

        initialize: function() {
            this._$field = this.$el.find("input");
            // this._$field.wrap('<div id="groupList">');

            this._$resultsList = $("<ul>");
            // this._$resultsList.css({
            //     'width': this._$field.width(),
            // });
            this._$resultsList.innerWidth(this._$field);
            this._$field.after(this._$resultsList);

            this.listenTo(this.collection, 'sync', this.render);
        },

        setMulti: function(multi) {
            this.multi = multi;
        },

        showResults: function(e) {
            var search = this._$field.val().trim().replace(/[-\\^$*+?.()|[\]{}]/g, "\\$&")
            if (this.multi) {
                var search = search.match(/[^,]* ?$/)[0].trim();
            }

            if (search.length <= 1) {
                // this._$more.hide();
                this._$resultsList.html('');
            }
            else {
                if (this.timeoutId != false) {
                    clearTimeout(this.timeoutId);
                    this.timeoutId = false;
                }

                var self = this;
                this.timeoutId = setTimeout(function() {
                    self.collection.search = search;
                    // self.collection.search = RegExp(r, "i").test(text);
                    self._$resultsList.html('');
                    self.collection.fetch({reset: true});
                }
                , 400);
            }
        },

        _hideResults: function(e) {
            this._$resultsList.hide();
            this._$resultsList.html('');
            this._$field.focus();
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
            if(this._$resultsList.is(':visible')) {
                this._selectResult($(e.target));
                this._replace($(e.target).text());
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
                        this._replace($selected.text());
                        this._hideResults();
                    }
                }
                else if (e.which === 27) { // Esc
                    this._hideResults();
                }
                else if (e.which === 38 || e.which === 40) { // Down/Up arrow
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
                this._template = Handlebars.compile(SEARCH_GROUPS_TPL);
            }
            return this._template(vars);
        },

        render: function() {
            if (this.collection.length) {
                var params = {};
                params.groups = [];

                this.collection.each(function(group) {
                    params.groups.push({
                        'gid': group.get('gid')
                    });

                }, this);


                this._$resultsList.append(this.template(params));
                this._$resultsList.show();
            }
        }
    });
    OCA.LotsOfUsers.GroupsView = GroupsView;

})(OC, OCA);

