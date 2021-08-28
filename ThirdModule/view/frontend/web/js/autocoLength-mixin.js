define(['uiComponent'],function (Component,$) {
    var Automixin = {

        handleAutocomplete: function (searchValue) {
            if (searchValue.length > 4)  {
                this._super();
            }else
                this.searchResultList([]);
        }
    };
    return function (Autotarget) {
        return Autotarget.extend(Automixin);
    }
});
