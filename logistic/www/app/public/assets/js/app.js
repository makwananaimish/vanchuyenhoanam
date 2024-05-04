// Column Chart
let _optionsColumnChart = {
    series: [
        {
            name: "Doanh Thu",
            data: [
                28877000, 29334000, 33233000, 36439000, 32675000, 32333000,
                33457000, 38345000, 36783000, 39457000, 22459000, 39840000,
            ],
        },
        {
            name: "Chi Phí",
            data: [
                12010000, 11313000, 14623000, 18935000, 17345000, 13465000,
                17813000, 19125000, 16256000, 20356000, 12233000, 14570000,
            ],
        },
        {
            name: "Công Nợ",
            data: [
                1201000, 1131000, 1462000, 1893000, 1734000, 1346000, 1783000,
                1912000, 1625000, 2035000, 1220003, 1457000,
            ],
        },
    ],
    chart: {
        fontFamily: "Manrope, sans-serif",
        type: "bar",
        height: 350,
        toolbar: {
            show: false,
        },
        zoom: {
            enabled: false,
        },
    },
    labels: {
        style: {
            fontSize: "14px",
        },
    },

    dataLabels: {
        enabled: false,
    },

    grid: {
        borderColor: "#DFE6E9",
        row: {
            opacity: 0.5,
        },
    },
    plotOptions: {
        bar: {
            horizontal: false,
            borderRadius: 2,
            columnWidth: "45%",
            endingShape: "rounded",
        },
        colors: {
            backgroundBarColors: ["#0063F7", "#00F7BF"],
        },
    },

    stroke: {
        show: true,
        width: 4,
        colors: ["transparent"],
    },
    xaxis: {
        axisTicks: {
            show: false,
            borderType: "solid",
            color: "#78909C",
            height: 6,
            offsetX: 0,
            offsetY: 0,
        },

        tickPlacement: "between",
        labels: {
            style: {
                colors: ["636E72"],
                fontSize: "14px",
            },
        },
        categories: [
            "Tháng 1",
            "Tháng 2",
            "Tháng 3",
            "Tháng 4",
            "Tháng 5",
            "Tháng 6",
            "Tháng 7",
            "Tháng 8",
            "Tháng 9",
            "Tháng 10",
            "Tháng 11",
            "Tháng 12",
        ],
    },
    legend: {
        horizontalAlign: "right",
        // offsetX: 40,
        position: "top",
        markers: {
            radius: 12,
        },
    },
    yaxis: {
        labels: {
            style: {
                colors: ["636E72"],
                fontSize: "14px",
            },
            formatter: (value) => {
                return (
                    new Intl.NumberFormat("vi-VN").format(Math.round(value)) +
                    "đ"
                );
            },
        },

        min: 0,
        max: 400000000,
        tickAmount: 6,
    },
    // colors: ["#F44336", "#E91E63", "#9C27B0"],
};

function drawChart() {
    if (document.querySelector("#column-chart2")) {
        const months = $("#select-months").val();
        const revenue = trucksGroup
            .map((x) => x.revenue)
            .slice(0, parseInt(months))
            .reverse();
        const cost = trucksGroup
            .map((x) => x.cost)
            .slice(0, parseInt(months))
            .reverse();
        const debt = trucksGroup
            .map((x) => x.debt)
            .slice(0, parseInt(months))
            .reverse();
        const trucks = trucksGroup
            .map((x) => x.total_trucks)
            .slice(0, parseInt(months))
            .reverse();
        const orders = trucksGroup
            .map((x) => x.total_orders)
            .slice(0, parseInt(months))
            .reverse();
        const customers = trucksGroup
            .map((x) => x.total_customers)
            .slice(0, parseInt(months))
            .reverse();
        const categories = trucksGroup
            .map((x) => `${x.month}-${x.year}`)
            .slice(0, parseInt(months))
            .reverse();

        const max = Math.max(...revenue);

        _optionsColumnChart.series = [
            {
                name: "Doanh Thu",
                data: revenue,
            },
            {
                name: "Chi Phí",
                data: cost,
            },
            {
                name: "Công Nợ",
                data: debt,
            },
            {
                name: "Xe",
                data: trucks,
            },
            {
                name: "Vận Đơn",
                data: orders,
            },
            {
                name: "Khách Hàng",
                data: customers,
            },
        ];

        _optionsColumnChart.xaxis.categories = categories;
        _optionsColumnChart.yaxis.max = max;

        let chart = new ApexCharts(
            document.querySelector("#column-chart2"),
            _optionsColumnChart
        );
        chart.render();
    }

    if (document.querySelector("#customers-chart")) {
        const optionsColumnChart = {
            series: [],
            chart: {
                fontFamily: "Manrope, sans-serif",
                type: "bar",
                height: 350,
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false,
                },
            },
            labels: {
                style: {
                    fontSize: "14px",
                },
            },

            dataLabels: {
                enabled: false,
            },

            grid: {
                borderColor: "#DFE6E9",
                row: {
                    opacity: 0.5,
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 2,
                    columnWidth: "45%",
                    endingShape: "rounded",
                },
                colors: {
                    backgroundBarColors: ["#0063F7", "#00F7BF"],
                },
            },

            stroke: {
                show: true,
                width: 4,
                colors: ["transparent"],
            },
            xaxis: {
                axisTicks: {
                    show: false,
                    borderType: "solid",
                    color: "#78909C",
                    height: 6,
                    offsetX: 0,
                    offsetY: 0,
                },

                tickPlacement: "between",
                labels: {
                    style: {
                        colors: ["636E72"],
                        fontSize: "14px",
                    },
                },
                categories: [
                    "Tháng 1",
                    "Tháng 2",
                    "Tháng 3",
                    "Tháng 4",
                    "Tháng 5",
                    "Tháng 6",
                    "Tháng 7",
                    "Tháng 8",
                    "Tháng 9",
                    "Tháng 10",
                    "Tháng 11",
                    "Tháng 12",
                ],
            },
            legend: {
                horizontalAlign: "right",
                // offsetX: 40,
                position: "top",
                markers: {
                    radius: 12,
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: ["636E72"],
                        fontSize: "14px",
                    },
                    // formatter: (value) => {
                    //     return value.toFixed(0);
                    // },
                },

                min: 0,
                max: 400000000,
                tickAmount: 6,
            },
        };

        const months = $("#select-months").val();

        const categories = trucksGroup
            .map((x) => `${x.date}`)
            .slice(0, parseInt(months));
        // .reverse();

        const totalCustomers = trucksGroup
            .map((x) => x.total_customers)
            .slice(0, parseInt(months));
        // .reverse();

        const max = Math.max(...totalCustomers);

        optionsColumnChart.series = [
            {
                name: "Tổng khách hàng kho Bằng Tường",
                data: totalCustomers,
            },
        ];

        optionsColumnChart.xaxis.categories = categories;
        optionsColumnChart.yaxis.max = max;

        let chart = new ApexCharts(
            document.querySelector("#customers-chart"),
            optionsColumnChart
        );
        chart.render();
    }

    if (document.querySelector("#revenue-chart")) {
        const optionsColumnChart = {
            series: [],
            chart: {
                fontFamily: "Manrope, sans-serif",
                type: "bar",
                height: 350,
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false,
                },
            },
            labels: {
                style: {
                    fontSize: "14px",
                },
            },

            dataLabels: {
                enabled: false,
            },

            grid: {
                borderColor: "#DFE6E9",
                row: {
                    opacity: 0.5,
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 2,
                    columnWidth: "45%",
                    endingShape: "rounded",
                },
                colors: {
                    backgroundBarColors: ["#0063F7", "#00F7BF"],
                },
            },

            stroke: {
                show: true,
                width: 4,
                colors: ["transparent"],
            },
            xaxis: {
                axisTicks: {
                    show: false,
                    borderType: "solid",
                    color: "#78909C",
                    height: 6,
                    offsetX: 0,
                    offsetY: 0,
                },

                tickPlacement: "between",
                labels: {
                    style: {
                        colors: ["636E72"],
                        fontSize: "14px",
                    },
                },
                categories: [
                    "Tháng 1",
                    "Tháng 2",
                    "Tháng 3",
                    "Tháng 4",
                    "Tháng 5",
                    "Tháng 6",
                    "Tháng 7",
                    "Tháng 8",
                    "Tháng 9",
                    "Tháng 10",
                    "Tháng 11",
                    "Tháng 12",
                ],
            },
            legend: {
                horizontalAlign: "right",
                // offsetX: 40,
                position: "top",
                markers: {
                    radius: 12,
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: ["636E72"],
                        fontSize: "14px",
                    },
                    formatter: (value) => {
                        return (
                            new Intl.NumberFormat("vi-VN").format(
                                Math.round(value)
                            ) + "đ"
                        );
                    },
                },

                min: 0,
                max: 400000000,
                tickAmount: 6,
            },
        };

        const months = $("#select-months").val();
        const revenue = trucksGroupBangTuong
            .map((x) => x.revenue)
            .slice(0, parseInt(months))
            .reverse();

        const categories = trucksGroupBangTuong
            .map((x) => `${x.month}-${x.year}`)
            .slice(0, parseInt(months))
            .reverse();

        const max = Math.max(...revenue);

        optionsColumnChart.series = [
            {
                name: "Doanh Thu",
                data: revenue,
            },
        ];

        optionsColumnChart.xaxis.categories = categories;
        optionsColumnChart.yaxis.max = max;

        let chart = new ApexCharts(
            document.querySelector("#revenue-chart"),
            optionsColumnChart
        );
        chart.render();
    }

    if (document.querySelector("#orders-bang-tuong-chart")) {
        const optionsColumnChart = {
            series: [],
            chart: {
                fontFamily: "Manrope, sans-serif",
                type: "bar",
                height: 350,
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false,
                },
            },
            labels: {
                style: {
                    fontSize: "14px",
                },
            },

            dataLabels: {
                enabled: false,
            },

            grid: {
                borderColor: "#DFE6E9",
                row: {
                    opacity: 0.5,
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 2,
                    columnWidth: "45%",
                    endingShape: "rounded",
                },
                colors: {
                    backgroundBarColors: ["#0063F7", "#00F7BF"],
                },
            },

            stroke: {
                show: true,
                width: 4,
                colors: ["transparent"],
            },
            xaxis: {
                axisTicks: {
                    show: false,
                    borderType: "solid",
                    color: "#78909C",
                    height: 6,
                    offsetX: 0,
                    offsetY: 0,
                },

                tickPlacement: "between",
                labels: {
                    style: {
                        colors: ["636E72"],
                        fontSize: "14px",
                    },
                },
                categories: [
                    "Tháng 1",
                    "Tháng 2",
                    "Tháng 3",
                    "Tháng 4",
                    "Tháng 5",
                    "Tháng 6",
                    "Tháng 7",
                    "Tháng 8",
                    "Tháng 9",
                    "Tháng 10",
                    "Tháng 11",
                    "Tháng 12",
                ],
            },
            legend: {
                horizontalAlign: "right",
                // offsetX: 40,
                position: "top",
                markers: {
                    radius: 12,
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: ["636E72"],
                        fontSize: "14px",
                    },
                    // formatter: (value) => {
                    //     return (
                    //         new Intl.NumberFormat("vi-VN").format(
                    //             Math.round(value)
                    //         ) + "đ"
                    //     );
                    // },
                },

                min: 0,
                max: 400000000,
                tickAmount: 6,
            },
        };

        const months = $("#select-months").val();
        const ordersBangTuong = trucksGroupBangTuong
            .map((x) => x.total_orders_bang_tuong)
            .slice(0, parseInt(months))
            .reverse();
        const orders = trucksGroupBangTuong
            .map((x) => x.total_orders)
            .slice(0, parseInt(months))
            .reverse();

        const categories = trucksGroupBangTuong
            .map((x) => `${x.month}-${x.year}`)
            .slice(0, parseInt(months))
            .reverse();

        const max = Math.max(...orders);

        optionsColumnChart.series = [
            {
                name: "Vận đơn kho Bằng Tường",
                data: ordersBangTuong,
            },
            {
                name: "Tổng vận đơn",
                data: orders,
            },
        ];

        optionsColumnChart.xaxis.categories = categories;
        optionsColumnChart.yaxis.max = max;

        let chart = new ApexCharts(
            document.querySelector("#orders-bang-tuong-chart"),
            optionsColumnChart
        );
        chart.render();
    }

    if (document.querySelector("#revenue-chart-from-to")) {
        const optionsColumnChart = {
            series: [],
            chart: {
                fontFamily: "Manrope, sans-serif",
                type: "bar",
                height: 350,
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false,
                },
            },
            labels: {
                style: {
                    fontSize: "14px",
                },
            },

            dataLabels: {
                enabled: false,
            },

            grid: {
                borderColor: "#DFE6E9",
                row: {
                    opacity: 0.5,
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 2,
                    columnWidth: "45%",
                    endingShape: "rounded",
                },
                colors: {
                    backgroundBarColors: ["#0063F7", "#00F7BF"],
                },
            },

            stroke: {
                show: true,
                width: 4,
                colors: ["transparent"],
            },
            xaxis: {
                axisTicks: {
                    show: false,
                    borderType: "solid",
                    color: "#78909C",
                    height: 6,
                    offsetX: 0,
                    offsetY: 0,
                },

                tickPlacement: "between",
                labels: {
                    style: {
                        colors: ["636E72"],
                        fontSize: "14px",
                    },
                },
                categories: [],
            },
            legend: {
                horizontalAlign: "right",
                // offsetX: 40,
                position: "top",
                markers: {
                    radius: 12,
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: ["636E72"],
                        fontSize: "14px",
                    },
                    formatter: (value) => {
                        return (
                            new Intl.NumberFormat("vi-VN").format(
                                Math.round(value)
                            ) + "đ"
                        );
                    },
                },

                min: 0,
                max: 400000000,
                tickAmount: 6,
            },
        };

        const ratio = (
            (100 * revenueChartFromToData.revenue2) /
            revenueChartFromToData.revenue1
        ).toFixed(2);

        const categories = [
            `${revenueChartFromToData.from1} - ${revenueChartFromToData.from2}`,
            `${revenueChartFromToData.to1} - ${revenueChartFromToData.to2} \n (${ratio}%)`,
        ];

        const max = Math.max(
            ...[
                revenueChartFromToData.revenue1,
                revenueChartFromToData.revenue2,
            ]
        );

        optionsColumnChart.series = [
            {
                name: "Doanh Thu",
                data: [
                    revenueChartFromToData.revenue1,
                    revenueChartFromToData.revenue2,
                ],
            },
        ];

        optionsColumnChart.xaxis.categories = categories;
        optionsColumnChart.yaxis.max = max;

        let chart = new ApexCharts(
            document.querySelector("#revenue-chart-from-to"),
            optionsColumnChart
        );
        chart.render();
    }
}

$(document).ready(function () {
    new ClipboardJS(".btn");

    drawChart();

    $(".select2").select2({});

    $(".select2-customer").each(function () {
        $(this).select2({
            dropdownParent: $(this).parents(".modal"),
            width: "100%",
        });
    });

    $('input[name="departure_dates"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: "Clear",
        },
    });
    if (
        $('input[name="departure_date_from"]').val() &&
        $('input[name="departure_date_to"]').val()
    ) {
        $('input[name="departure_dates"]').daterangepicker({
            startDate: $('input[name="departure_date_from"]').val(),
            endDate: $('input[name="departure_date_to"]').val(),
            locale: {
                cancelLabel: "Clear",
            },
        });
    }

    $('input[name="departure_dates"]').on(
        "apply.daterangepicker",
        function (ev, picker) {
            $(this).val(
                picker.startDate.format("MM/DD/YYYY") +
                    " - " +
                    picker.endDate.format("MM/DD/YYYY")
            );

            $('input[name="departure_date_from"]').val(
                picker.startDate.format("MM/DD/YYYY")
            );
            $('input[name="departure_date_to"]').val(
                picker.endDate.format("MM/DD/YYYY")
            );
            $(".filter-trucks").submit();
        }
    );

    $('input[name="arrival_dates"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: "Clear",
        },
    });

    if (
        $('input[name="arrival_date_from"]').val() &&
        $('input[name="arrival_date_to"]').val()
    ) {
        $('input[name="arrival_dates"]').daterangepicker({
            startDate: $('input[name="arrival_date_from"]').val(),
            endDate: $('input[name="arrival_date_to"]').val(),
        });
    }

    $('input[name="arrival_dates"]').on(
        "apply.daterangepicker",
        function (ev, picker) {
            $(this).val(
                picker.startDate.format("MM/DD/YYYY") +
                    " - " +
                    picker.endDate.format("MM/DD/YYYY")
            );

            $('input[name="arrival_date_from"]').val(
                picker.startDate.format("MM/DD/YYYY")
            );
            $('input[name="arrival_date_to"]').val(
                picker.endDate.format("MM/DD/YYYY")
            );
            $(".filter-trucks").submit();
        }
    );

    if (isAdmin) {
        $(init);
    }

    function init() {
        $(".droppable-area1, .droppable-area4, .droppable-area5")
            .sortable({
                connectWith: ".connected-sortable",
                stack: ".connected-sortable ul",
                stop: function (event, ui) {
                    updateLocation(ui);
                },
            })
            .disableSelection();
        $(".droppable-area2, .droppable-area4, .droppable-area5")
            .sortable({
                connectWith: ".connected-sortable",
                stack: ".connected-sortable ul",
                stop: function (event, ui) {
                    updateLocation(ui);
                },
            })
            .disableSelection();
        $(".droppable-area3, .droppable-area4, .droppable-area5")
            .sortable({
                connectWith: ".connected-sortable",
                stack: ".connected-sortable ul",
                stop: function (event, ui) {
                    updateLocation(ui);
                },
            })
            .disableSelection();
    }

    // Show modal
    const regex = /#.*$/;
    const sections = window.location.href.match(regex);
    if (sections) {
        if (sections[0]) {
            if ($(sections[0])) {
                $(sections[0]).modal("show");
            }
        }
    }

    // Expand and collapse
    $(".customer-row td").click(function () {
        if ($(this).find(".fa.fa-caret-right").length === 0) return;

        const row = $(this).closest(".customer-row");
        const expand = row.attr("data-expand");
        const expandable = row.attr("data-expandable");
        const id = row.attr("data-id");

        if (expandable === "1") {
            if (expand === "1") {
                $(`.customer-row-header[data-id="${id}"]`).removeClass(
                    "d-table-row"
                );
                $(`.customer-row-order[data-id="${id}"]`).removeClass(
                    "d-table-row"
                );
                $(`.customer-row-header[data-id="${id}"]`).addClass("d-none");
                $(`.customer-row-order[data-id="${id}"]`).addClass("d-none");

                $(this).removeClass("customer-row-active");
                $(`.customer-row-header[data-id="${id}"]`).removeClass(
                    "customer-row-active"
                );
                $(`.customer-row-order[data-id="${id}"]`).removeClass(
                    "customer-row-active"
                );
                $(
                    `.customer-row[data-id="${id}"] .customer-row-caret-right`
                ).removeClass("rotate-90deg");
            } else {
                $(`.customer-row-header[data-id="${id}"]`).removeClass(
                    "d-none"
                );
                $(`.customer-row-order[data-id="${id}"]`).removeClass("d-none");
                $(`.customer-row-header[data-id="${id}"]`).addClass(
                    "d-table-row"
                );
                $(`.customer-row-order[data-id="${id}"]`).addClass(
                    "d-table-row"
                );

                row.addClass("customer-row-active");
                $(`.customer-row-header[data-id="${id}"]`).addClass(
                    "customer-row-active"
                );
                $(`.customer-row-order[data-id="${id}"]`).addClass(
                    "customer-row-active"
                );
                $(
                    `.customer-row[data-id="${id}"] .customer-row-caret-right`
                ).addClass("rotate-90deg");
            }

            row.attr("data-expand", expand === "0" ? "1" : "0");
        }
    });

    // Format currency
    $(".input-currency").each(function () {
        _numberFormat(this);
    });

    $(".input-currency").on("input", function () {
        _numberFormat(this);
    });

    // Scroll to element
    const searchParams = new URLSearchParams(window.location.search);
    const scrollTo = searchParams.get("scroll_to");
    if ($(scrollTo).length) {
        $("html, body").animate(
            {
                scrollTop: $(".list-trucks").offset().top,
            },
            100
        );
    }

    // Search order
    const orderId = searchParams.get("order_id");
    if ($(`.customer-row[data-id="${orderId}"]`).length) {
        $(`.customer-row-header[data-id="${orderId}"]`).removeClass("d-none");
        $(`.customer-row-order[data-id="${orderId}"]`).removeClass("d-none");
        $(`.customer-row-header[data-id="${orderId}"]`).addClass("d-table-row");
        $(`.customer-row-order[data-id="${orderId}"]`).addClass("d-table-row");

        $(`.customer-row[data-id="${orderId}"]`).addClass(
            "customer-row-active"
        );
        $(`.customer-row-header[data-id="${orderId}"]`).addClass(
            "customer-row-active"
        );
        $(`.customer-row-order[data-id="${orderId}"]`).addClass(
            "customer-row-active"
        );
        $(
            `.customer-row[data-id="${orderId}"] .customer-row-caret-right`
        ).addClass("rotate-90deg");
        const expand = $(`.customer-row[data-id="${orderId}"]`).attr(
            "data-expand"
        );
        $(`.customer-row[data-id="${orderId}"]`).attr(
            "data-expand",
            expand === "0" ? "1" : "0"
        );
    }

    $("#address-notification").modal("show");
});

function deleteCustomer(id) {
    if (confirm("Chắc chắn xóa bản ghi này ?")) {
        $(`#frm-${id}`).submit();
    }
}

function deleteTruck(id) {
    if (confirm("Chắc chắn xóa bản ghi này ?")) {
        $(`#frm-${id}`).submit();
    }
}

function deleteUser(id) {
    if (confirm("Chắc chắn xóa bản ghi này ?")) {
        $(`#frm-${id}`).submit();
    }
}

function deleteLocation(id) {
    if (confirm("Chắc chắn xóa bản ghi này ?")) {
        $(`#frm-${id}`).submit();
    }
}

function deleteOrder(id) {
    if (confirm("Chắc chắn xóa bản ghi này ?")) {
        $(`#frm-${id}`).submit();
    }
}

function deleteRecord(id) {
    if (confirm("Chắc chắn xóa bản ghi này ?")) {
        $(`#frm-${id}`).submit();
    }
}

function deletePack(id) {
    if (confirm("Chắc chắn xóa bản ghi này ?")) {
        $(`#frm-del-pack-${id}`).submit();
    }
}

function _delete(id) {
    if (confirm("Chắc chắn xóa bản ghi này ?")) {
        $(`#frm-${id}`).submit();
    }
}

function filterCustomer(el) {
    $(".customer-filter-form").submit();
}

function filterTrucks() {
    $(".filter-trucks").submit();
}

function isInt(value) {
    return (
        !isNaN(value) &&
        parseInt(Number(value)) == value &&
        !isNaN(parseInt(value, 10))
    );
}

function numberFormat(el) {
    let val = $(el).val().replace(/\./g, "");

    if (!isInt(val)) {
        val = val.replace(/.$/, "");
        $(el).val(val);
    }

    // if (val.length > 0) {
    let format = new Intl.NumberFormat("vi-VN").format(val);
    $(el).val(format);
    // }
}

function formatCurrency(val) {
    return new Intl.NumberFormat("vi-VN").format(val);
}

function _numberFormat(el) {
    numberFormat(el);
    const name = $(el).attr("name");
    const val = $(el).val().replace(/\./g, "");
    $(el).next(`input[type="hidden"][name="${name}"]`).remove();
    $(`<input type="hidden" name="${name}" value="${val}">`).insertAfter($(el));
}

function getCsrfToken() {
    return $('meta[name="csrf-token"]').attr("content");
}

function updateLocation(ui) {
    const truckId = $(ui.item[0]).attr("data-id");
    const locationId = $(ui.item[0]).parents(".column").attr("data-id");
    return _updateLocation(truckId, locationId);
}

function _updateLocation(truckId, locationId) {
    return $.post(`/trucks/${truckId}/update_location`, {
        current_location_id: locationId,
        _token: getCsrfToken(),
    });
}

function updateOrderStatus(el, id) {
    const status = el.checked ? 1 : 0;

    return $.post(`/orders/${id}/update_status`, {
        status,
        _token: getCsrfToken(),
    });
}

function updatePackStatus(el, id) {
    const status = el.checked ? 1 : 0;

    return $.post(`/packs/${id}/update_status`, {
        status,
        _token: getCsrfToken(),
    });
}

async function _deliveryOrder(
    id,
    deliveryDate,
    driverPhone,
    licensePlateNumber
) {
    return $.post(`/orders/${id}/update_status`, {
        status: 1,
        _token: getCsrfToken(),
        delivery_date: deliveryDate,
        driver_phone: driverPhone,
        license_plate_number: licensePlateNumber,
    });
}

async function _delivery(
    ids = [],
    deliveryDate,
    driverPhone,
    licensePlateNumber
) {
    return Promise.all(
        ids.map((id) =>
            _deliveryOrder(id, deliveryDate, driverPhone, licensePlateNumber)
        )
    );
}

async function delivery(deliveryDate, driverPhone, licensePlateNumber) {
    const ids = $(".delivery-input:checked")
        .map(function () {
            return $(this).val();
        })
        .get();

    return _delivery(ids, deliveryDate, driverPhone, licensePlateNumber);
}

async function bulkDelivery(thiz) {
    $(thiz).prop("disabled", true);
    $(thiz).html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...`
    );

    try {
        const deliveryDate = $("#delivery_date").val();
        const driverPhone = $("input[name='driver_phone']").val();
        const licensePlateNumber = $(
            "input[name='license_plate_number']"
        ).val();

        await delivery(deliveryDate, driverPhone, licensePlateNumber);
    } catch (error) {}

    $(thiz).prop("disabled", false);
    $(thiz).html("Xác nhận trả hàng");
    window.location.reload();
}

function updateNoteInList(id, note) {
    return $.post(`/orders/${id}/update_note_in_list`, {
        note_in_list: note,
        _token: getCsrfToken(),
    });
}

function updateNoteInTruck(id, note) {
    return $.post(`/orders/${id}/update_note_in_truck`, {
        note_in_truck: note,
        _token: getCsrfToken(),
    });
}

function updateNoteInVnInventory(id, note) {
    return $.post(`/orders/${id}/update_note_in_vn_inventory`, {
        note_in_vn_inventory: note,
        _token: getCsrfToken(),
    });
}

function exportExcel(type, elt) {
    TableToExcel.convert(elt);
}

function _payPayables(ids) {
    return $.post(`/payables/pay`, {
        order_ids: ids,
        _token: getCsrfToken(),
    });
}

function _getDebt(ids) {
    return $.post(`/payables/debt`, {
        order_ids: ids,
        _token: getCsrfToken(),
    });
}

async function payPayables() {
    const ids = $("input[name='order_ids[]']:checked")
        .map(function () {
            return $(this).val();
        })
        .get();

    const resp = await _payPayables(ids);

    if (!resp.success) {
        alert(`${resp.error}`);
    } else {
        alert("Thanh toán thành công");
        return window.location.reload();
    }
}

async function getDebt() {
    const ids = $("input[name='order_ids[]']:checked")
        .map(function () {
            return $(this).val();
        })
        .get();

    const resp = await _getDebt(ids);

    const debt = resp.debt;

    $(".total-debt").text(formatCurrency(debt));
}

function showDeclarationModal(orderId) {
    $(`#create-order-declaration input[name="order_id"]`).val(orderId);
}

async function checkOrderCodes(thiz) {
    $("#merge-orders textarea")
        .removeClass("text-info")
        .removeClass("fw-bolder");
    $("#merge-orders .invalid-feedback strong").html("");
    $("#merge-orders .code-label").text("Mã vận đơn:");

    const codes = $(thiz).val().split("\n");

    console.log(codes);

    const resp = await $.post(`/orders/merge/check_order_codes`, {
        order_codes: codes,
        _token: getCsrfToken(),
    });

    $("#merge-orders .invalid-feedback strong").html(
        resp.other_codes.join("<br>")
    );

    $("#merge-orders .code-label").text(
        `Mã vận đơn(tìm thấy ${resp.codes.length} mã):`
    );
    $("#merge-orders textarea").addClass("text-info").addClass("fw-bolder");
    $(thiz).val(resp.codes.join("\n"));
}

async function bulkMerge(truckId, codes) {
    return $.post(`/orders/merge/bulk`, {
        truck_id: truckId,
        codes,
        _token: getCsrfToken(),
    });
}

async function _bulkMerge(thiz) {
    $("#merge-orders .alert-danger").addClass("d-none");
    $("#merge-orders .alert-success").addClass("d-none");

    $(thiz).prop("disabled", true);
    $(thiz).html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...`
    );

    const truckId = $("#merge-orders select").val();
    const codes = $("#merge-orders textarea").val().split("\n");

    const resp = await bulkMerge(truckId, codes);

    if (!resp.success) {
        $("#merge-orders .alert-danger").removeClass("d-none");
        $("#merge-orders .alert-danger").html(resp.errors.join("<br>"));
    } else {
        $("#merge-orders .alert-success").removeClass("d-none");
        $("#merge-orders .alert-success").html("Ghép đơn thành công");
        window.location.href = `/trucks/${truckId}`;
    }

    $(thiz).prop("disabled", false);
    $(thiz).html("Ghép");
}

$(".check-all").on("click", function () {
    $(this)
        .closest("table")
        .find("input[name='order_ids[]']")
        .prop("checked", this.checked);

    getDebt();
});

$("input[name='order_ids[]']").on("click", function () {
    $(this)
        .closest("table")
        .find(".check-all")
        .prop(
            "checked",
            $(this)
                .closest("table")
                .find("tbody input[name='order_ids[]']:checked").length ==
                $(this).closest("table").find("tbody input[name='order_ids[]']")
                    .length
        );

    getDebt();
});

$(".check-all-delivery-input").on("click", function () {
    $(this)
        .closest("table")
        .find(".delivery-input")
        .prop("checked", this.checked);
});

$(".delivery-input").on("click", function () {
    $(this)
        .closest("table")
        .find(".check-all-delivery-input")
        .prop(
            "checked",
            $(this).closest("table").find("tbody .delivery-input:checked")
                .length ==
                $(this).closest("table").find("tbody .delivery-input").length
        );
});

$("#check-all").on("click", function () {
    $(this)
        .closest("table")
        .find(".form-check-input")
        .prop("checked", this.checked);
});

$(".form-check-input").on("click", function () {
    $(this)
        .closest("table")
        .find("#check-all")
        .prop(
            "checked",
            $(this).closest("table").find("tbody .form-check-input:checked")
                .length ==
                $(this).closest("table").find("tbody .form-check-input").length
        );
});

async function notifyAddress() {
    const ids = $("input[name='order_ids[]']:checked")
        .map(function () {
            return $(this).val();
        })
        .get();

    $("#notify-address #ids").val(ids);
}

$(function () {
    $(".datepicker").daterangepicker();
});
