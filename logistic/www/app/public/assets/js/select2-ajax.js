$(document).ready(function () {
    $(".select2-orders").select2({
        ajax: {
            url: "/api/orders",
            dataType: "json",
            // delay: 250,
            data: function (params) {
                var query = {
                    code: params.term,
                };

                return query;
            },
            processResults: function (data, params) {
                return {
                    results: data.map((item) => {
                        return { id: item.id, text: item.code };
                    }),
                    pagination: {
                        more: false,
                    },
                };
            },
            cache: true,
        },
        placeholder: "Tìm vận đơn",
    });

    $(".select2-orders").on("select2:select", function (e) {
        var data = e.params.data;
        window.location.href = `/orders/${data.id}`;
    });
});
