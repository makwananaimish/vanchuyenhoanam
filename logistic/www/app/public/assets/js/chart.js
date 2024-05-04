const topHunterBonusChart = document.getElementById("top-hunter-bonus-chart");
const topSaleChart = document.getElementById("top-sale-chart");

const iconMoney = new Image();
iconMoney.src = "/assets/img/icon-money1.svg";

if (topHunterBonusChart) {
    let data = [];

    for (const [key, value] of Object.entries(hunters)) {
        console.log(`${key}: ${value}`);

        data.push({
            name: value.name,
            avatar_url: value.avatar_url,
            revenue: value.revenue,
        });
    }

    data = _.orderBy(data, ["revenue"]);
    console.log("data", data);

    var ctx = topHunterBonusChart.getContext("2d");

    const images = data.map((x, index) => {
        const img = new Image();
        img.src = x.avatar_url;

        return img;
    });

    const annotations = data.map((x, index) => {
        let xMin = index + 1;
        let xMax = index + 1;

        let revenue = data[index].revenue;
        let yMin = revenue + 30000000;
        let yMax = revenue + 30000000;

        return {
            type: "label",
            drawTime: "afterDraw",

            content: images[index],

            width: 50,
            height: 50,

            xMin,
            xMax,
            yMin,
            yMax,
        };
    });
    console.log("annotations", annotations);

    const annotationsLabel = data.map((x, index) => {
        let xMin = index + 1;
        let xMax = index + 1;

        let revenue = data[index].revenue;
        let yMin = revenue - 10000000;
        let yMax = revenue - 10000000;

        // xMin = xMin - 0.1;
        // xMax = xMax - 0.1;

        return {
            type: "label",

            xMin,
            xMax,
            yMin,
            yMax,

            color: "#3f3122",

            content: [
                x.name,
                new Intl.NumberFormat("vi-VN", {
                    // style: "currency",
                    // currency: "VND",
                }).format(x.revenue),
            ],

            font: {
                size: 18,
            },

            position: "end",

            textAlign: "center",
        };
    });

    const annotationsIconMoney = data.map((x, index) => {
        let xMin = index + 1;
        let xMax = index + 1;

        let revenue = data[index].revenue;
        let yMin = revenue - 10000000;
        let yMax = revenue - 10000000;

        // xMin += 0.05;
        // xMax += 0.05;

        return {
            type: "label",
            drawTime: "afterDraw",

            content: iconMoney,

            width: 24,
            height: 24,

            xMin,
            xMax,
            yMin,
            yMax,

            padding: 8,
            yAdjust: -24,
            xAdjust: +24,

            position: "center",
        };
    });

    let labels = data.map((x, index) => index + 1).reverse();
    labels = [null, ...labels, null];
    console.log("labels", labels);

    data = data.map((x) => x.revenue);
    data = [null, ...data, null];
    console.log("data", data);

    const max = Math.max(...data);

    const plugin = {
        id: "customCanvasBackgroundColor",
        beforeDraw: (chart, args, options) => {
            const { ctx } = chart;
            ctx.save();
            ctx.globalCompositeOperation = "destination-over";
            ctx.fillStyle = options.color || "#fff8ef";
            ctx.fillRect(0, 0, chart.width, chart.height);
            ctx.restore();
        },
    };

    const config = {
        type: "line",
        data: {
            labels,
            datasets: [
                {
                    label: "",
                    data,
                    fill: false,
                    borderColor: "#fec775",
                    tension: 0.1,
                },
            ],
        },
        options: {
            scales: {
                xAxes: [
                    {
                        type: "linear",
                        position: "bottom",
                        display: false,
                    },
                ],

                y: {
                    type: "linear",
                    min: -20000000,
                    max: max * 1.5,
                    ticks: {
                        // stepSize: 10000000,
                    },
                },
            },

            plugins: {
                customCanvasBackgroundColor: {
                    color: "#fff8ef",
                },

                annotation: {
                    annotations: [
                        ...annotations,
                        ...annotationsLabel,
                        ...annotationsIconMoney,
                    ],
                },
            },
        },

        plugins: [plugin],
    };

    new Chart(ctx, config);
}

if (topSaleChart) {
    let data = [];

    for (const [key, value] of Object.entries(topSale)) {
        console.log(`${key}: ${value}`);

        data.push({
            name: value.name,
            avatar_url: value.avatar_url,
            revenue: value.revenue,
        });
    }

    data = _.orderBy(data, ["revenue"]);
    console.log("data", data);

    var ctx = topSaleChart.getContext("2d");

    const images = data.map((x, index) => {
        const img = new Image();
        img.src = x.avatar_url;

        return img;
    });

    const annotations = data.map((x, index) => {
        let xMin = index + 1;
        let xMax = index + 1;

        let revenue = data[index].revenue;
        let yMin = revenue + 100000000;
        let yMax = revenue + 100000000;

        return {
            type: "label",
            drawTime: "afterDraw",

            content: images[index],

            width: 50,
            height: 50,

            xMin,
            xMax,
            yMin,
            yMax,
        };
    });
    console.log("annotations", annotations);

    const annotationsLabel = data.map((x, index) => {
        let xMin = index + 1;
        let xMax = index + 1;

        let revenue = data[index].revenue;
        let yMin = revenue - 10000000;
        let yMax = revenue - 10000000;

        // xMin = xMin - 0.1;
        // xMax = xMax - 0.1;

        return {
            type: "label",

            xMin,
            xMax,
            yMin,
            yMax,

            color: "#3f3122",

            content: [
                x.name,
                new Intl.NumberFormat("vi-VN", {
                    // style: "currency",
                    // currency: "VND",
                }).format(x.revenue),
            ],

            font: {
                size: 12,
            },

            position: "end",

            textAlign: "center",
        };
    });

    const annotationsIconMoney = data.map((x, index) => {
        let xMin = index + 1;
        let xMax = index + 1;

        let revenue = data[index].revenue;
        let yMin = revenue - 10000000;
        let yMax = revenue - 10000000;

        // xMin += 0.05;
        // xMax += 0.05;

        return {
            type: "label",
            drawTime: "afterDraw",

            content: iconMoney,

            width: 18,
            height: 18,

            xMin,
            xMax,
            yMin,
            yMax,

            padding: 6,
            yAdjust: -18,
            xAdjust: +18,

            position: "center",
        };
    });

    let labels = data.map((x, index) => index + 1).reverse();
    labels = [null, ...labels, null];
    console.log("labels", labels);

    data = data.map((x) => x.revenue);
    data = [null, ...data, null];
    console.log("data", data);

    const max = Math.max(...data);

    const plugin = {
        id: "customCanvasBackgroundColor",
        beforeDraw: (chart, args, options) => {
            const { ctx } = chart;
            ctx.save();
            ctx.globalCompositeOperation = "destination-over";
            ctx.fillStyle = options.color || "#fff8ef";
            ctx.fillRect(0, 0, chart.width, chart.height);
            ctx.restore();
        },
    };

    const config = {
        type: "line",
        data: {
            labels,
            datasets: [
                {
                    label: "",
                    data,
                    fill: false,
                    borderColor: "#fec775",
                    tension: 0.1,
                },
            ],
        },
        options: {
            scales: {
                xAxes: [
                    {
                        type: "linear",
                        position: "bottom",
                        display: false,
                    },
                ],

                y: {
                    type: "linear",
                    min: -20000000,
                    max: max * 1.5,
                    ticks: {
                        // stepSize: 10000000,
                    },
                },
            },

            plugins: {
                customCanvasBackgroundColor: {
                    color: "#fff8ef",
                },

                annotation: {
                    annotations: [
                        ...annotations,
                        ...annotationsLabel,
                        ...annotationsIconMoney,
                    ],
                },
            },
        },

        plugins: [plugin],
    };

    new Chart(ctx, config);
}
