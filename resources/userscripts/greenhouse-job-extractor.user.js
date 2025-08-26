// ==UserScript==
// @name         Greenhouse Job Extractor
// @namespace    http://tampermonkey.net/
// @version      1.0
// @description  Inject buttons to copy/view/optimize Greenhouse job data
// @match        *://*greenhouse.io/*/jobs/*
// @match        *://*.greenhouse.io/*/jobs/*
// @require      https://rdo.blog.br/common-job-tools.js?v=0.31
// @grant        none
// ==/UserScript==

window.addEventListener('load', () =>
    window.insertButtons({
        role: () => document.querySelector('.job__title h1')?.textContent.trim(),
        company: () => document.title.split(' at ').pop()?.trim(),
        location: () => document.querySelector('.job__location')?.textContent.trim(),
        description: () => document.querySelector('.job__description')?.textContent.trim(),
        attachButtonsTo: {
            selector: '.job__tags',
            position: 'above'
        }
    })
);
