import axios from "axios";
import { ReadWriteApi } from "../../api";
import { IMedia } from "./interfaces";

/**
 * Media API service.
 */
export class MediaAPI extends ReadWriteApi<IMedia> {
  private static _instance: MediaAPI;

  public static get Instance(): MediaAPI {
    return this._instance || (this._instance = new this());
  }

  private constructor() {
    super("admin/media");
  }

  createMedia(application: IMedia, file: any) {
    let formData = new FormData();
    formData.append("file", file);
    formData.append("dto", JSON.stringify(application));

    return axios.post<IMedia>(`${this.base}/${this.rwPrefix}`, formData);
  }

  updateMedia(application: IMedia, file: any) {
    let formData = new FormData();
    formData.append("file", file);
    formData.append("dto", JSON.stringify(application));

    return axios.post<IMedia>(`${this.base}/${this.rwPrefix}/${application.id}`, formData);
  }
};
