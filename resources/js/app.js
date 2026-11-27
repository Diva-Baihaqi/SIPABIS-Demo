import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

// Konfigurasi Default Chart.js Profesional BPS
Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
Chart.defaults.color = '#64748B'; // slate-500
Chart.defaults.responsive = true;
Chart.defaults.maintainAspectRatio = false;

// Tooltip Styling
Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.92)'; // slate-900
Chart.defaults.plugins.tooltip.titleColor = '#F8FAFC';
Chart.defaults.plugins.tooltip.bodyColor = '#E2E8F0';
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.cornerRadius = 10;
Chart.defaults.plugins.tooltip.boxPadding = 6;
Chart.defaults.plugins.tooltip.usePointStyle = true;
Chart.defaults.plugins.tooltip.titleFont = { weight: 'bold', size: 12 };
Chart.defaults.plugins.tooltip.bodyFont = { size: 12 };

// Legend Styling
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.padding = 16;
Chart.defaults.plugins.legend.labels.font = { weight: '600', size: 12 };

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();
