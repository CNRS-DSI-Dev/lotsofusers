(function(OC, OCA) {

    // ******************************** User model
    var UserModel = OC.Backbone.Model.extend({
        getGroupsString: function() {
            if (this.get('groups').length < 1) {
                return '';
            }

            return this.get('groups').reduce(function(result, group){
                return result + ", " + group;
            })
        }

        // initialize: function(){
        //     console.log("Initialize UserModel!");
        // }

    });
    OCA.LotsOfUsers.UserModel = UserModel;

})(OC, OCA);

