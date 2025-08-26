// ==UserScript==
// @name         GeekHunter Job Extractor
// @namespace    http://tampermonkey.net/
// @version      1.2
// @description  Robust job extractor for GeekHunter with stable selectors
// @match        *://www.geekhunter.com.br/v2/en/*/jobs/*
// @require      https://rdo.blog.br/common-job-tools.js
// @grant        none
// ==/UserScript==

window.addEventListener('load', () =>
    window.insertButtons({
        role: () => {
            const heading = Array.from(document.querySelectorAll('p, h1, h2'))
                .find(el => el.textContent.trim() && el.textContent.length < 100); // first concise block
            return heading?.textContent.trim() || '';
        },

        company: () => {
            const match = window.location.pathname.match(/\/v2\/en\/([^/]+)\/jobs/);
            return match ? decodeURIComponent(match[1]).replace(/-/g, ' ').trim() : '';
        },

        location: () => {
            // Find the SVG labeled with a building icon (job location)
            const svgGroups = Array.from(document.querySelectorAll('svg')).map(svg => svg.closest('div'));
            let locationValue = '';

            const locationSVG = svgGroups.find(g => g?.innerHTML.includes('d="M2 27h28'));
            const locationBlock = locationSVG.nextElementSibling;
            const locationPlace = locationBlock?.nextElementSibling;

            if (!locationBlock) return '';

            locationValue = locationBlock.innerText;
            if (!locationPlace) return locationValue;

            locationValue += ` - ${locationPlace.innerText}`;

            // Get the next paragraph siblings only (not nested content)
            return locationValue;
        },

        description: () => {
            const getSection = (headingText) => {
                const el = Array.from(document.querySelectorAll('p, h2, h3'))
                    .find(n => n.textContent.trim().toLowerCase().includes(headingText.toLowerCase()));
                return el?.closest('div');
            };

            const extractText = el =>
                Array.from(el?.querySelectorAll('p, li'))
                    .map(n => n.textContent.trim())
                    .filter(Boolean)
                    .join('\n\n');

            const sections = ['Tarefas', 'Responsabilidades', 'Requisitos'];
            return sections
                .map(getSection)
                .map(extractText)
                .filter(Boolean)
                .join('\n\n');
        },

        attachButtonsTo: {
            selector: '.chakra-container',
            position: 'above'
        }
    })
);
