// Fungsi untuk membuat Bar Chart
export function eventBarChart(config) {
    return {
        chartInstance: null,

        init() {
            // Memastikan canvas ada sebelum di-render
            const canvas = this.$refs.canvas;
            if (!canvas) return;

            const ctx = canvas.getContext("2d");

            // Ambil constructor Chart
            this.chartInstance = new window.Chart(ctx, {
                type: "bar",
                data: {
                    labels: config.labels,
                    datasets: [
                        {
                            label: "Jumlah Event",
                            data: config.data,
                            backgroundColor: config.bg,
                            borderColor: config.border,
                            borderWidth: 0,
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.8,
                            categoryPercentage: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: "#000666",
                            titleFont: {
                                family: "Inter, sans-serif",
                                size: 12,
                                weight: "bold",
                            },
                            bodyFont: {
                                family: "Inter, sans-serif",
                                size: 12,
                            },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: (context) =>
                                    `${context.raw} Event Terdaftar`,
                            },
                        },
                    },
                    scales: {
                        y: {
                            min: 0,
                            ticks: {
                                precision: 0,
                                color: "rgba(148, 163, 184, 0.7)",
                                font: {
                                    family: "Inter, sans-serif",
                                    size: 11,
                                    weight: "500",
                                },
                            },
                            grid: {
                                color: "rgba(148, 163, 184, 0.08)",
                                drawBorder: false,
                            },
                        },
                        x: {
                            ticks: {
                                color: "#000666",
                                font: {
                                    family: "Inter, sans-serif",
                                    size: 12,
                                    weight: "700",
                                },
                            },
                            grid: { display: false },
                        },
                    },
                },
            });

            this.$watch(
                () => this.$wire.chartConfig,
                (newConfig) => {
                    if (this.chartInstance && newConfig) {
                        this.chartInstance.data.labels = newConfig.labels;
                        this.chartInstance.data.datasets[0].data =
                            newConfig.data;
                        this.chartInstance.data.datasets[0].backgroundColor =
                            newConfig.bg;
                        this.chartInstance.data.datasets[0].borderColor =
                            newConfig.border;

                        this.chartInstance.update();
                    }
                },
            );
        },

        destroy() {
            this.chartInstance?.destroy();
        },
    };
}

// Fungsi untuk membuat Line Chart
export function eventLineChart(config) {
    return {
        chartInstance: null,

        init() {
            const canvas = this.$refs.canvas;
            if (!canvas) return;

            const ctx = this.$refs.canvas.getContext("2d");

            // Membuat efek glowing shadow
            const gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, "rgba(0, 6, 102, 0.12)");
            gradient.addColorStop(1, "rgba(0, 6, 102, 0.0)");

            this.chartInstance = new window.Chart(ctx, {
                type: "line",
                data: {
                    labels: config.labels,
                    datasets: [
                        {
                            label: "Organisasi Aktif",
                            data: config.data,
                            borderColor: "#000666",
                            borderWidth: 2,
                            fill: true,
                            backgroundColor: gradient,
                            tension: 0.4,
                            pointBackgroundColor: "#000666",
                            pointBorderColor: "#fff",
                            pointBorderWidth: 1.5,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: "#000666",
                            pointHoverBorderColor: "#fff",
                            pointHoverBorderWidth: 2,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: "index",
                            intersect: false,
                            padding: 8,
                            backgroundColor: "#1E1E2A",
                            titleColor: "#fff",
                            bodyColor: "#fff",
                            displayColors: false,
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: "rgba(0, 6, 102, 0.5)",
                                font: {
                                    family: "Inter, sans-serif",
                                    size: 10,
                                    weight: "500",
                                },
                            },
                        },
                        y: {
                            min: 0,
                            grid: {
                                color: "rgba(0, 6, 102, 0.04)",
                                drawBorder: false,
                            },
                            ticks: {
                                precision: 0,
                                color: "rgba(0, 6, 102, 0.5)",
                                font: {
                                    family: "Inter, sans-serif",
                                    size: 10,
                                    weight: "500",
                                },
                            },
                        },
                    },
                },
            });

            this.$watch(
                () => this.$wire.config,
                (newConfig) => {
                    if (this.chartInstance && newConfig) {
                        this.chartInstance.data.labels = newConfig.labels;
                        this.chartInstance.data.datasets[0].data =
                            newConfig.data;

                        this.chartInstance.update();
                    }
                },
            );
        },

        destroy() {
            this.chartInstance?.destroy();
        },
    };
}

// Fungsi untuk membuat Pie Chart
export function eventPieChart(config) {
    return {
        chartInstance: null,
        currentTotal: config.total, 

        init() {
            const canvas = this.$refs.canvas;
            if (!canvas) return;
            const ctx = canvas.getContext("2d");

            const centerTextPlugin = {
                id: "centerText",
                
                afterDraw: (chart) => {
                    const {
                        ctx,
                        chartArea: { top, bottom, left, right },
                    } = chart;
                    const centerX = (left + right) / 2;
                    const centerY = (top + bottom) / 2;

                    ctx.save();

                    // Angka total
                    ctx.font = "bold 28px Inter, sans-serif";
                    ctx.fillStyle = "#000666";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "middle";
                    ctx.fillText(this.currentTotal, centerX, centerY - 9);

                    // Label "TOTAL"
                    ctx.font = "600 10px Inter, sans-serif";
                    ctx.letterSpacing = "0.05em";
                    ctx.fillStyle = "rgba(100, 116, 139, 0.55)";
                    ctx.fillText("TOTAL", centerX, centerY + 13);

                    ctx.restore();
                },
            };

            this.chartInstance = new window.Chart(ctx, {
                type: "doughnut",
                plugins: [centerTextPlugin],
                data: {
                    labels: config.labels,
                    datasets: [
                        {
                            data: config.data,
                            backgroundColor: config.colors || config.bg,
                            borderWidth: 0,
                            borderRadius: 5,
                            spacing: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: "74%",
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: "#000666",
                            titleFont: {
                                family: "Inter, sans-serif",
                                size: 12,
                                weight: "bold",
                            },
                            bodyFont: { family: "Inter, sans-serif", size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: (context) => {
                                    const total = context.dataset.data.reduce(
                                        (a, b) => a + b,
                                        0,
                                    );
                                    const pct = total > 0 ? Math.round((context.raw / total) * 100) : 0;
                                    return `${context.raw} Event · ${pct}%`;
                                },
                            },
                        },
                    },
                },
            });

            this.$watch(
                () => this.$wire.chartConfig,
                (newConfig) => {
                    if (this.chartInstance && newConfig) {
                        this.currentTotal = newConfig.total;
                        this.chartInstance.data.labels = newConfig.labels;
                        this.chartInstance.data.datasets[0].data =
                            newConfig.data;
                        this.chartInstance.data.datasets[0].backgroundColor =
                            newConfig.bg;

                        this.chartInstance.update();
                    }
                },
            );
        },

        destroy() {
            this.chartInstance?.destroy();
        },
    };
}