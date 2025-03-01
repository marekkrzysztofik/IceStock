import { useToast } from "primevue/usetoast";

export function useGlobalToast() {
    const toast = useToast();

    const showSuccess = (summary = "Sukces!", detail = "Operacja zakończona pomyślnie.") => {
        toast.add({ severity: "success", summary, detail, life: 3000 });
    };

    const showError = (summary = "Błąd!", detail = "Wystąpił nieoczekiwany problem.") => {
        toast.add({ severity: "error", summary, detail, life: 3000 });
    };

    return { showSuccess, showError };
}
