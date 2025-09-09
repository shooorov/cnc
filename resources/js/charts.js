/* Imports */

import * as am5 from "@amcharts/amcharts5";
import * as am5xy from "@amcharts/amcharts5/xy";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";
// import * as am5percent from "@amcharts/amcharts5/Percent";

export function MaybeDisposeRoot(htmlElm) {
    am5.array.each(am5.registry.rootElements, function (root) {
        if (root && root.dom.id == htmlElm) {
            root.dispose();
        }
    });
}

// export function ChartPie(htmlElm, data) {
//     MaybeDisposeRoot(htmlElm);

//     /* Chart code */
//     // Create root element
//     // https://www.amcharts.com/docs/v5/getting-started/#Root_element
//     let root = am5.Root.new(htmlElm);

//     // Set themes
//     // https://www.amcharts.com/docs/v5/concepts/themes/
//     root.setThemes([
//         am5themes_Animated.new(root)
//     ]);

//   // Create chart
//   // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/
//   let chart = root.container.children.push(am5percent.PieChart.new(root, {
//     layout: root.verticalLayout
//   }));

//   // Create series
//   // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Series
//   let series = chart.series.push(am5percent.PieSeries.new(root, {
//     valueField: "value",
//     categoryField: "category"
//   }));

//   // Set data
//   // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data
//   series.data.setAll(data);

//   // Play initial series animation
//   // https://www.amcharts.com/docs/v5/concepts/animations/#Animation_of_series
//   series.appear(1000, 100);
// }

export function ChartLine(htmlElm, data, xAxisName, yAxisName) {
    // Example Data Format
    // let data = [{
    //         "xAxisValue": "2013-01-27",
    //         "yAxisValue": 84
    //     }
    // ];

    MaybeDisposeRoot(htmlElm);
    /* Chart code */
    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    let root = am5.Root.new(htmlElm);

    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([am5themes_Animated.new(root)]);

    //   root.dateFormatter.setAll({
    //     dateFormat: "yyyy",
    //     dateFields: ["valueX"]
    //   });

    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    let chart = root.container.children.push(
        am5xy.XYChart.new(root, {
            focusable: true,
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            pinchZoomX: true,
        })
    );

    let easing = am5.ease.linear;

    // Create axes
    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
    let xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
    xRenderer.labels.template.setAll({
        rotation: -45,
        centerY: am5.p50,
        centerX: am5.p100,
        paddingRight: 5,
    });

    // let xRenderer = am5xy.AxisRendererX.new(root, {});
    let yRenderer = am5xy.AxisRendererY.new(root, {});

    let xAxis = chart.xAxes.push(
        am5xy.DateAxis.new(root, {
            maxDeviation: 0.1,
            groupData: false,
            baseInterval: {
                timeUnit: "hour",
                count: 1,
            },
            renderer: xRenderer,
            tooltip: am5.Tooltip.new(root, {}),
        })
    );

    let yAxis = chart.yAxes.push(
        am5xy.ValueAxis.new(root, {
            maxDeviation: 0.2,
            renderer: yRenderer,
        })
    );

    xAxis.children.push(
        am5.Label.new(root, {
            text: xAxisName,
            x: am5.p50,
            centerX: am5.p50,
        })
    );

    yAxis.children.moveValue(
        am5.Label.new(root, {
            text: yAxisName,
            rotation: -90,
            y: am5.p50,
            centerX: am5.p50,
        }),
        0
    );

    // Add series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
    let series = chart.series.push(
        am5xy.LineSeries.new(root, {
            minBulletDistance: 10,
            connect: false,
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: "yAxisValue",
            valueXField: "xAxisValue",
            tooltip: am5.Tooltip.new(root, {
                pointerOrientation: "horizontal",
                labelText: "{valueY}",
            }),
        })
    );

    series.fills.template.setAll({
        fillOpacity: 0.2,
        visible: true,
    });

    series.strokes.template.setAll({
        strokeWidth: 2,
    });

    //   series.columns.template.setAll({
    // 	tooltipText: "{valueY}"
    //   });

    // Set up data processor to parse string dates
    // https://www.amcharts.com/docs/v5/concepts/data/#Pre_processing_data
    series.data.processor = am5.DataProcessor.new(root, {
        dateFormat: data.length ? data[0]["dateFormat"] : "",
        dateFields: ["xAxisValue"],
    });

    series.data.setAll(data);

    series.bullets.push(function () {
        let circle = am5.Circle.new(root, {
            radius: 4,
            fill: root.interfaceColors.get("background"),
            stroke: series.get("fill"),
            strokeWidth: 2,
        });

        return am5.Bullet.new(root, {
            sprite: circle,
        });
    });

    // Add cursor
    // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
    let cursor = chart.set(
        "cursor",
        am5xy.XYCursor.new(root, {
            xAxis: xAxis,
            behavior: "none",
        })
    );
    cursor.lineY.set("visible", false);

    // add scrollbar
    chart.set(
        "scrollbarX",
        am5.Scrollbar.new(root, {
            orientation: "horizontal",
        })
    );

    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/
    chart.appear(1000, 100);
}

export function ChartColumn(htmlElm, data, xAxisName, yAxisName) {
    // Example Data Format
    // let data = [{
    //   xAxis: "USA",
    //   yAxis: 2025
    // }];

    MaybeDisposeRoot(htmlElm);

    /* Chart code */
    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    let root = am5.Root.new(htmlElm);

    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([am5themes_Animated.new(root)]);

    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    let chart = root.container.children.push(
        am5xy.XYChart.new(root, {
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            pinchZoomX: true,
        })
    );

    // Add cursor
    // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
    let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
    cursor.lineY.set("visible", false);

    // Create axes
    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
    let xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
    xRenderer.labels.template.setAll({
      rotation: -45,
      centerY: am5.p50,
      centerX: am5.p100,
      paddingRight: 5
    });
    // let xRenderer = am5xy.AxisRendererX.new(root, {});
    let yRenderer = am5xy.AxisRendererY.new(root, {});

    let xAxis = chart.xAxes.push(
        am5xy.CategoryAxis.new(root, {
            maxDeviation: 0.3,
            categoryField: "xAxisValue",
            renderer: xRenderer,
            tooltip: am5.Tooltip.new(root, {}),
        })
    );

    let yAxis = chart.yAxes.push(
        am5xy.ValueAxis.new(root, {
            maxDeviation: 0.3,
            renderer: yRenderer,
        })
    );

    xAxis.children.push(
        am5.Label.new(root, {
            text: xAxisName,
            x: am5.p50,
            centerX: am5.p50,
        })
    );

    yAxis.children.moveValue(
        am5.Label.new(root, {
            text: yAxisName,
            rotation: -90,
            y: am5.p50,
            centerX: am5.p50,
        }),
        0
    );

    // Create series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
    let series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
            name: "Series 1",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: "yAxisValue",
            sequencedInterpolation: true,
            categoryXField: "xAxisValue",
            tooltip: am5.Tooltip.new(root, {
                labelText: "{valueY}",
            }),
        })
    );

    series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5 });
    series.columns.template.adapters.add("fill", function (fill, target) {
        return chart.get("colors").getIndex(series.columns.indexOf(target));
    });

    series.columns.template.adapters.add("stroke", function (stroke, target) {
        return chart.get("colors").getIndex(series.columns.indexOf(target));
    });

    xAxis.data.setAll(data);
    series.data.setAll(data);

    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/
    series.appear(1000);
    chart.appear(1000, 100);
}
