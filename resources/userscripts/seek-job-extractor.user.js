// ==UserScript==
// @name         Seek Job Extractor
// @namespace    http://tampermonkey.net/
// @version      1.0
// @description  Extract job info from Seek with copy/view buttons
// @match        *://www.seek.com.au/job/*
// @require      https://rdo.blog.br/common-job-tools.js
// @grant        none
// ==/UserScript==

window.addEventListener('load', () => {
    window.insertButtons({
        role: () => document.querySelector('[data-automation="job-detail-title"]')?.textContent.trim(),
        company: () => document.querySelector('[data-automation="advertiser-name"]')?.textContent.trim(),
        location: () => document.querySelector('[data-automation="job-detail-location"]')?.textContent.trim(),
        description: () => {
            const container = document.querySelector('[data-automation="jobAdDetails"]');
            if (!container) return '';
            return Array.from(container.querySelectorAll('p, li')).map(n => n.textContent.trim()).join('\n\n');
        },
        attachButtonsTo: {
            selector: '[data-automation="job-detail-title"]',
            position: 'above'
        }
    });
});
