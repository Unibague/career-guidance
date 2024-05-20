import {checkIfModelHasEmptyProperties, toObjectRequest} from "@/HelperFunctions";

export default class AcademicArea {
    toObjectRequest() {
        return toObjectRequest(this);
    }

    hasEmptyProperties() {
        return checkIfModelHasEmptyProperties(this);
    }

    static fromModel(model) {
        return new AcademicArea(model.id, model.name, model.message, model.academic_programs);
    }

    constructor(id = null,  name = '', message = '', academicPrograms = null) {
        this.id = id;
        this.name = name;
        this.message = message;
        this.academicPrograms = academicPrograms;

        this.dataStructure = {
            id: null,
            name: 'required',
            message: 'required',
            }
    }
}
