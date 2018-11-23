export default {
    twoWay: true,
    priority: 1000,

    params: ['options'],

    bind: function () {
        let self = this;
        let el = this.el;
        let options = $.extend(this.params.options, {format: 'YYYY-MM-DD H:mm'});
        $(el)
            .datetimepicker(options)
            .on('dp.change', function () {
                self.set(el.value);
            });
    },
    update: function (value) {
        $(this.el).val(value);
    },
    unbind: function () {
        $(this.el).data("DateTimePicker").destroy();
    }
}