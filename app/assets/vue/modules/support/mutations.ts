import { AxiosError } from "axios";

import { IListResponse } from "../../api";
import { IError } from "../../interfaces/error";
import { IMutations } from "../../store/mutations";

import { ISupportState } from "./state";
import { ISupport } from "./interfaces";

export interface ISupportMutations extends IMutations {
  SUPPORT_SEND_REQUEST_PENDING(state: ISupportState, data: ISupport): void;
  SUPPORT_SEND_REQUEST_SUCCESS(state: ISupportState): void;
  SUPPORT_SEND_REQUEST_ERROR(state: ISupportState, error?: AxiosError<IError>): void;
}

export const SupportMutationsDefault: ISupportMutations = {

  SUPPORT_SEND_REQUEST_PENDING(state: ISupportState, data: ISupport = null) {
    state.isLoading = true;
    state.clearError();
    state.item = data;
  },
  SUPPORT_SEND_REQUEST_SUCCESS(state: ISupportState) {
    state.isLoading = false;
    state.clearError();
    state.item = null;
  },
  SUPPORT_SEND_REQUEST_ERROR(state: ISupportState, error?: AxiosError<IError>) {
    state.isLoading = false;
    state.saveError(error);
    state.item = null;
  },

};
