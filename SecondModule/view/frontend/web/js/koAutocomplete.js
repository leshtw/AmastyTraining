define([
    'uiComponent',
    'jquery',
    'mage/url'
], function (Component,$,urlBuilder) {
    return Component.extend({
        defaults: {
            searchText: '',
            searchUrl: urlBuilder.build('alex/Autoco/Autoco'),
            searchResultList: '',

        },
        initObservable: function () {
            this._super();
            this.observe(['searchText','searchResultList']);
            return this;
        },
        initialize: function () {
            this._super();
            this.searchText.subscribe(this.handleAutocomplete.bind(this));
        },
        handleAutocomplete: function (searchValue) {
            if (searchValue.length > 2)  {
                $.getJSON(this.searchUrl, {
                    item: searchValue   //данные переводваемые на саревер
                }, function (data) {
                    this.searchResultList(data);

                }.bind(this));
            }else
                this.searchResultList([]);
        }
    });
});
