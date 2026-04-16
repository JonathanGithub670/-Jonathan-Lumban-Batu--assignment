'use strict';

/**
 * Plot result from the beam analysis calculation into a graph
 */
class AnalysisPlotter {
    constructor(container) {
        this.container = container;
        this.chart = null;
    }

    /**
     * Plot equation.
     *
     * @param {Object{beam : Beam, load : float, equation: Function}}  The equation data
     */
    plot(data) {
        var beam = data.beam;
        var condition = document.getElementById('condition').value;
        var L1 = beam.primarySpan;
        var L = L1;
        if (condition === 'two-span-unequal') {
            L += (beam.secondarySpan || 0);
        }

        var points = [];
        var step = L / 100;
        
        var maxVal = 0;
        var minVal = 0;
        var hasJump = (condition === 'two-span-unequal');
        
        for (var x = 0; x <= L; x += step) {
            // Deteksi secara akurat batas potongan balok antara L1 dan L2
            if (hasJump && x > L1 && (x - step) < L1) {
                var ptLeft = data.equation(L1);
                points.push({ x: L1, y: ptLeft.y });
                points.push({ x: null, y: null }); // Putuskan garis
                var ptRight = data.equation(L1 + 0.000001);
                points.push({ x: L1, y: ptRight.y });
            }
            if (hasJump && Math.abs(x - L1) < 0.0001) {
                var ptLeft = data.equation(L1);
                points.push({ x: L1, y: ptLeft.y });
                points.push({ x: null, y: null }); // Putuskan garis
                var ptRight = data.equation(L1 + 0.000001);
                points.push({ x: L1, y: ptRight.y });
                continue;
            }

            var point = data.equation(x);
            points.push({ x: point.x, y: point.y });
            if (point.y > maxVal) maxVal = point.y;
            if (point.y < minVal) minVal = point.y;
        }

        // Add 10% padding to bounds, but keep 0 if it doesn't cross 0
        var boundMaxY = maxVal > 0 ? maxVal * 1.1 : 0;
        var boundMinY = minVal < 0 ? minVal * 1.1 : 0;

        // Get canvas context
        var canvas = document.getElementById(this.container);
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        
        var chartStatus = Chart.getChart(this.container);
        if (chartStatus != undefined) {
            chartStatus.destroy();
        }

        var title = this.container.replace(/_/g, ' ').toUpperCase();
        var isDeflection = this.container.includes('deflection');
        var isShear = this.container.includes('shear');
        var isMoment = this.container.includes('moment');

        var color = 'red';
        var fillColor = isDeflection ? 'transparent' : 'rgba(211,211,211,0.5)';

        this.chart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: title,
                    data: points,
                    borderColor: color,
                    backgroundColor: fillColor,
                    showLine: true,
                    fill: !isDeflection,
                    pointRadius: 0,
                    borderWidth: 2,
                    spanGaps: false,
                    tension: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: { display: true, text: 'Span (m)' }
                    },
                    y: {
                        reverse: false,
                        suggestedMin: boundMinY,
                        suggestedMax: boundMaxY,
                        title: {
                            display: true,
                            text: isDeflection ? 'Deflection (mm)' : 
                                  isShear ? 'Force (kN)' : 'Moment (kN.m)'
                        }
                    }
                }
            }
        });
    }
}