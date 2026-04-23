# DOCUMENTATION GUIDELINE (BASE.MD)

Use this file as the primary template for creating any new documentation within the Inox ERP project to ensure consistency, clarity, and professionalism.

---

## 1. FILE STRUCTURE (Standard Header & Footer)

### HEADER SECTION
Every document must begin with the following metadata header:
```markdown
---
Project: Nayati Inox ERP
Module: [Module Name]
Version: 1.0.0
Last Updated: [YYYY-MM-DD]
Status: [Draft/Review/Final]
---

# [DOCUMENT TITLE]
```

### FOOTER SECTION
Every document must end with the following official footer:
```markdown
---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
```

---

## 2. WRITING STANDARDS

- **Language:** Use formal and technical English (unless Indonesian is explicitly requested for specific user guides).
- **Code Blocks:** Always specify the language type (e.g., ` ```php ` or ` ```typescript `).
- **Logic Explanation:** Prioritize "Why" (architectural intent) over just "How" (implementation steps).
- **Clarity:** Use tables, mermaid diagrams, or bullet points to explain complex workflows.

---

## 3. FILE NAMING CONVENTION

- Use **UPPERCASE** for filenames: `ROLE.MD`, `AUTH.MD`, `WAREHOUSE.MD`.
- Categorize by language within `docs/indonesia/` or `docs/english/` folders if necessary.

---

## 4. PRE-PUBLISH CHECKLIST
- [ ] Header is correctly filled (Version & Date).
- [ ] Code snippets are verified against the latest codebase.
- [ ] Internal links between documents are functional.
- [ ] Official footer is present.
