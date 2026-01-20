# Performance Audit Guide

## Goals

- Lighthouse targets: Performance 90+, Best Practices 95+, SEO 95+
- Bundle size tracking per widget and base assets

---

## Bundle Size Report (Vite)

1) Build assets:
```
npm run build
```

2) Generate report:
```
npm run bundle:report
```

Output:
- `storage/app/performance/bundle-report.json`
 - Shown in **Admin → Performance** under "Bundle Size Report"

---

## Lighthouse Manual Audit

1) Open the public page in Chrome/Brave
2) Open DevTools → Lighthouse
3) Run with:
   - Device: Desktop
   - Throttling: Simulated
4) Save results as JSON

Suggested pages:
- `/demo-cafe`
- A heavy content page (gallery/map)

### Import into Dashboard

1) Save the JSON file locally (e.g. `C:\reports\demo-cafe-lighthouse.json`)
2) Import it:
```
php artisan performance:lighthouse:import C:\reports\demo-cafe-lighthouse.json
```
3) Open **Admin → Performance** to see the latest scores.

---

## Notes

- If scores drop, compare `bundle-report.json` to find regressions.
- Re-run after any build or major frontend change.
