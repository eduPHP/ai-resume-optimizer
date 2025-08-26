// ==UserScript==
// @name         Glassdoor Job Extractor
// @namespace    http://tampermonkey.net/
// @version      1.0
// @description  Extract job info from Glassdoor job view pages
// @match        *://*.glassdoor.com.br/job-listing/*
// @match        *://*.glassdoor.com/job-listing/*
// @require      https://rdo.blog.br/common-job-tools.js
// @grant        none
// ==/UserScript==

function cleanGlassdoorURL() {
    const url = new URL(location.href);
    const jobId = url.searchParams.get('jl');
    const pathname = url.pathname;
    const cleanUrl = `${url.origin}${pathname}?jl=${jobId}`;

    if (location.href !== cleanUrl) {
        history.replaceState(null, '', cleanUrl);
    }
}

window.addEventListener('load', () => {
    cleanGlassdoorURL();
    window.insertButtons({
        role: () =>
            document.querySelector('h1[id^="jd-job-title"]')?.textContent.trim(),
        company: () =>
            document.querySelector('div[class*="employerNameHeading"] h4')?.textContent.trim(),
        location: () =>
            document.querySelector('[data-test="location"]')?.textContent.trim(),
        description: () => {
            const el = document.querySelector('[data-brandviews*="jobview-description"]');
            return el ? el.innerText.trim() : '';
        },
        attachButtonsTo: {
            selector: 'h1[id^="jd-job-title"]',
            position: 'above',
        },
    })
});
