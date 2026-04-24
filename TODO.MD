# Refactoring & Abstraction TODO

This list identifies areas in the Vue.js codebase that should be refactored or abstracted to follow DRY (Don't Repeat Yourself) principles and improve maintainability.

## 1. Utility: Date Formatting [DONE]
**Issue:** The `formatDate` function is manually redefined across multiple index and show pages.
**Recommendation:** Move to a centralized utility or composable (e.g., `resources/js/lib/formatters.ts`).
**Implementation:** Created `resources/js/utils/date.ts`.

- `resources/js/pages/Utility/AuditTrail/Index.vue` (Refactored)
- `resources/js/pages/Utility/RolePermission/Index.vue` (Refactored)
- `resources/js/pages/Utility/RolePermission/Show.vue` (No usage found)
- `resources/js/pages/Utility/User/Show.vue` (No usage found)

## 2. Composable: DataTable Server-Side Logic [DONE]
**Issue:** The logic for `updateRoute`, `onPage`, `onSort`, and `performSearch` (with debounce) is virtually identical in all paginated Index pages.
**Recommendation:** Create a `useDataTable` or `usePaginate` composable.
**Implementation:** Created `resources/js/composables/common/useDataTable.ts` and `resources/js/components/common/table/StandardDataTable.vue`.

- `resources/js/pages/Utility/AuditTrail/Index.vue` (Refactored)
- `resources/js/pages/Utility/RolePermission/Index.vue` (Refactored)
- `resources/js/pages/Utility/User/Index.vue` (Refactored)

## 3. Component: Reusable Form Fields
**Issue:** Labels and input field wrappers (flex containers for labels, inputs, and error messages) are repeated manually throughout form components.
**Recommendation:** Create a wrapper component (e.g., `FormField.vue`) to handle labels, errors, and layout consistency.

- `resources/js/components/Users/UserForm.vue` (Lines 61-155)
- `resources/js/components/Roles/RoleForm.vue` (Lines 40-100)

## 4. Component: Standard Action Column
**Issue:** Action columns in DataTables (view buttons, icons) use manual templates with similar styling.
**Recommendation:** Create a reusable `ActionColumn` or `ViewButton` component.

- `resources/js/pages/Utility/AuditTrail/Index.vue` (Lines 242-248)
- `resources/js/pages/Utility/RolePermission/Index.vue` (Lines 94-100)

## 5. Abstraction: Confirmation Logic [DONE]
**Issue:** `useConfirm` logic for "Discard Changes" is repeated in different forms.
**Recommendation:** Create a `useAppConfirm` helper or a standard configuration for common dialogs.
**Implementation:** Created `resources/js/composables/common/useCancelConfirm.ts`.

- `resources/js/components/Users/UserForm.vue` (Refactored)
- `resources/js/components/Roles/RoleForm.vue` (Refactored)

## 6. Styles: Postfix Tailwind Standards
**Issue:** Some files might still use prefix `!` instead of the project's canonical `!` postfix (e.g., `bg-white!` vs `!bg-white`).
**Recommendation:** Audit and standardize all utility classes.

- Check `resources/js/pages/Utility/AuditTrail/Index.vue` (Multiple lines in template and style block)
- Check `resources/js/components/Roles/RoleForm.vue`
