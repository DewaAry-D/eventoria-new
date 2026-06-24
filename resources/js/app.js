import { Chart, registerables } from "chart.js";

Chart.register(...registerables);
window.Chart = Chart;

import {
    eventBarChart,
    eventLineChart,
    eventPieChart,
} from "./charts/eventCharts";

document.addEventListener("alpine:init", () => {
    if (window.Alpine) {
        window.Alpine.data("eventBarChart", eventBarChart);
        window.Alpine.data("eventLineChart", eventLineChart);
        window.Alpine.data("eventPieChart", eventPieChart);
    }
});
