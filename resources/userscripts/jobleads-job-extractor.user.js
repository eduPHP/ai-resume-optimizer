// ==UserScript==
// @name         Jobleads Job Extractor
// @namespace    http://tampermonkey.net/
// @version      1.0
// @description  Extract job info from Jobleads and send to optimizer
// @match        *://www.jobleads.com/*/job/*
// @require      https://rdo.blog.br/common-job-tools.js
// @grant        none
// ==/UserScript==

window.addEventListener('load', () =>
    window.insertButtons({
        role: () =>
            document.querySelector('.job-preview__sticky h1')?.textContent.trim(),

        company: () =>
            document.querySelector('.job-preview__sticky h2')?.textContent.trim(),

        location: () => {
            const chips = document.querySelectorAll('[data-testid="job-card-chips"] h2 span');
            return chips?.[0]?.textContent.trim() || '';
        },

        description: () => {
            const content = document.querySelector('.new-job-preview-description-content') ||
                document.querySelector('[data-testid="job-card-summary"]');
            if (!content) return '';
            const elements = Array.from(content.querySelectorAll('p, li, div'));
            return elements.map(el => el.textContent.trim()).filter(Boolean).join('\n\n');
        },

        attachButtonsTo: {
            selector: '[data-testid="job-preview-card"]',
            position: 'above'
        }
    })
);
