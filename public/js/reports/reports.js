/**
 * Reports: the charts (amCharts 3).
 *
 * #reports-data holds the texts in the language of the page, and the data that the page
 * has from the start. The other charts ask the server for their data.
 * Category names come from the database, so the pop-up texts (HTML) escape them.
 */
(() => {
    'use strict';

    const { baseUrl, getJson } = window.Benefile;
    const dataElement = document.getElementById('reports-data');
    const report = dataElement ? JSON.parse(dataElement.textContent) : {};
    const texts = report.texts ?? {};

    const escape = (value) => {
        const element = document.createElement('span');
        element.textContent = value === null || value === undefined ? '' : String(value);
        return element.innerHTML;
    };

    const shadow = {
        filter: [{
            id: 'shadow',
            width: '200%',
            height: '200%',
            feOffset: { result: 'offOut', in: 'SourceAlpha', dx: 0, dy: 0 },
            feGaussianBlur: { result: 'blurOut', in: 'offOut', stdDeviation: 5 },
            feBlend: { in: 'SourceGraphic', in2: 'blurOut', mode: 'normal' },
        }],
    };

    /** A ring chart with a legend on the right. The slice under the pointer comes to the front. */
    function pieChart(id, data, { titleField = 'title', valueField = 'value' } = {}) {
        if (!document.getElementById(id)) {
            return;
        }
        const chart = window.AmCharts.makeChart(id, {
            type: 'pie',
            theme: 'light',
            fontSize: 16,
            startDuration: 0,
            addClassNames: true,
            legend: { position: 'right', marginRight: 100, autoMargins: false, fontSize: 16 },
            innerRadius: '30%',
            defs: shadow,
            dataProvider: data,
            titleField,
            valueField,
            outlineAlpha: 0.4,
            balloonFunction: (item) => `${escape(item.title)}<br><span style='font-size:14px'><b>${escape(item.value)}</b> (${escape(item.percents.toFixed(2))}%)</span>`,
            export: { enabled: true },
        });
        const toFront = (event) => {
            const wedge = event.dataItem.wedge.node;
            wedge.parentNode.appendChild(wedge);
        };
        chart.addListener('init', () => chart.legend.addListener('rollOverItem', toFront));
        chart.addListener('rollOverSlice', toFront);
    }

    /** A column chart. With rotate, the bars are horizontal. */
    function columnChart(id, data, { categoryField = 'title', valueField = 'value', title = '', suffix = '', rotate = false, theme = 'light', fontSize = 16, color, labelRotation = 0, categoryTitle } = {}) {
        if (!document.getElementById(id)) {
            return;
        }
        window.AmCharts.makeChart(id, {
            type: 'serial',
            theme,
            fontSize,
            rotate,
            ...(rotate ? {} : { marginRight: 70 }),
            gridAboveGraphs: rotate,
            dataProvider: data,
            valueAxes: [{ axisAlpha: 0, position: 'left', title, fontSize }],
            startDuration: 1,
            graphs: [{
                balloonFunction: (item) => `<b>${escape(item.category)}: ${escape(item.values.value)}</b>${suffix ? ` ${escape(suffix)}` : ''}`,
                fillColorsField: 'color',
                fillAlphas: 0.9,
                lineAlpha: 0.2,
                type: 'column',
                valueField,
                ...(color ? { lineColor: color } : {}),
            }],
            chartCursor: { categoryBalloonEnabled: false, cursorAlpha: 0, zoomable: false },
            categoryField,
            categoryAxis: { gridPosition: 'start', labelRotation, fontSize, ...(rotate ? { gridAlpha: 0 } : {}), ...(categoryTitle ? { title: categoryTitle } : {}) },
            export: { enabled: true },
        });
    }

    /** A line over time with a scrollbar and buttons for the period. */
    function stockChart(id, data, { field, title, fontSize = 11 }) {
        if (!document.getElementById(id)) {
            return;
        }
        const periods = texts.periods ?? {};
        window.AmCharts.makeChart(id, {
            type: 'stock',
            theme: 'light',
            fontSize,
            dataSets: [{
                color: '#b0de09',
                fieldMappings: [{ fromField: field, toField: field }],
                dataProvider: data,
                categoryField: 'per_month_date',
            }],
            panels: [{
                showCategoryAxis: true,
                title,
                eraseAll: false,
                stockGraphs: [{ id: 'g1', valueField: field, useDataSetColors: false, bullet: 'round' }],
                stockLegend: { valueTextRegular: ' ', markerType: 'none' },
                drawingIconsEnabled: false,
            }],
            chartScrollbarSettings: { graph: 'g1' },
            chartCursorSettings: { valueBalloonsEnabled: true },
            periodSelector: {
                position: 'bottom',
                periods: [
                    { period: 'DD', count: 10, label: periods.days },
                    { period: 'MM', count: 1, label: periods.month },
                    { period: 'YYYY', count: 1, label: periods.year },
                    { period: 'MAX', label: periods.max },
                ],
            },
        });
    }

    /** Draws a chart with the data that the server gives for it. */
    const fromServer = (path, draw) => getJson(`${baseUrl}/${path}`).then((data) => draw(data ?? [])).catch(() => {});

    // Data of the page
    pieChart('maritalStatusReport', report.maritalStatuses ?? []);
    pieChart('medicalStatusReport', report.visitLocations ?? []);
    pieChart('ageReport', report.ages ?? []);
    pieChart('legalStatusReport', report.legalStatuses ?? []);
    columnChart('benefiters-work-title-chart', report.workTitles ?? [], { title: texts.workTitle, suffix: texts.benefiters, rotate: true });
    columnChart('benefiters-per-medical-visits-chart', report.visitsPerBenefiter ?? [], { title: texts.visitsY, categoryTitle: texts.visitsX, suffix: texts.benefiters, color: '#3D6139' });

    // Data from the server
    fromServer('benefites-VS-education-Report-get-data', (data) => pieChart('benefiter_vs_education', data, {
        titleField: 'education_title',
        valueField: 'benefiters_count_with_this_education_title',
    }));
    fromServer('benefites-VS-doctor-Report-get-data', (data) => columnChart('benefiter_vs_doctor_type', data, {
        categoryField: 'doctor',
        valueField: 'count_benefiters_with_same_doctor',
        title: texts.doctorsTitle,
        fontSize: 16,
    }));
    fromServer('benefites-VS-ClinicalConditions-Report-get-data', (data) => columnChart('benefiter_vs_clinical_conditions', data, {
        categoryField: 'clinical_condition_name',
        valueField: 'clinical_condition_count',
        title: texts.numberOfBenefiters,
        rotate: true,
        theme: 'dark',
        fontSize: 14,
    }));
    fromServer('benefites-VS-phycological-support-type', (data) => columnChart('benefiter_vs_phycological_support', data, {
        categoryField: '$phycological_support_type',
        valueField: 'type_count',
        title: texts.numberOfBenefiters,
        rotate: true,
        color: '#A57251',
    }));
    fromServer('medical_visits-PER-month-Report-get-data', (data) => stockChart('medical_visits_per_month', data, {
        field: 'visits_per_month',
        title: texts.visitsPerMonth,
    }));
    fromServer('registrations-PER-month-Report-get-data', (data) => stockChart('registrations_per_month', data, {
        field: 'registrations_per_month',
        title: texts.registrationsPerMonth,
        fontSize: 16,
    }));
})();
