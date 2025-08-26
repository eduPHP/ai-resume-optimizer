// ==UserScript==
// @name         Indeed Job Extractor
// @namespace    http://tampermonkey.net/
// @version      1.4
// @description  Extract job info from Indeed with copy/view buttons above title
// @match        *://*.indeed.com/viewjob*
// @match        *://indeed.com/viewjob*
// @require      https://rdo.blog.br/common-job-tools.js?v=1.41
// @grant        none
// ==/UserScript==

window.addEventListener('load', () => window.insertButtons({
    role: () => document.querySelector('.jobsearch-JobInfoHeader-title')?.textContent.trim(),
    company: () => document.querySelector('div[data-testid="jobsearch-CompanyInfoContainer"] a')?.innerText.trim() || document.querySelector('[data-testid="inlineHeader-companyName"] span')?.innerText.trim(),
    location: () => {
        const location1 = document.querySelector('[data-testid="inlineHeader-companyLocation"]')?.innerText.trim();

        if (location1) return location1;

        const container = document.querySelector('[data-testid="jobsearch-CompanyInfoContainer"]');
        const divs = container?.querySelectorAll('div') || [];
        return divs[divs.length - 1]?.innerText.trim() || '';
    },
    description: () => {
        const jobSections = document.querySelectorAll('.jobsearch-JobComponent-description > *');
        let startIndex = [...jobSections].findIndex(n => n.textContent.includes('Full job description')) + 1;
        return [...jobSections].slice(startIndex).map(n => n.textContent.trim()).join('\n\n');
    },
    attachButtonsTo: {
        selector: '.jobsearch-JobInfoHeader-title',
        position: 'above'
    },
}));

