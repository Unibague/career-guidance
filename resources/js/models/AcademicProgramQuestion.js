import {checkIfModelHasEmptyProperties, toObjectRequest} from "@/HelperFunctions";

export default class AcademicProgramQuestion {
    toObjectRequest() {
        return toObjectRequest(this);
    }

    hasEmptyProperties() {
        return checkIfModelHasEmptyProperties(this);
    }

    static fromModel(model) {
        return new AcademicProgramQuestion(model.id, model.academic_program_code, model.name);
    }

    constructor(id = null,  programCode = '', name = '') {
        this.id = id;
        this.academic_program_code = programCode;
        this.name = name;

        this.dataStructure = {
            id: null,
            academic_program_code: 'required',
            name: 'required',
            }
    }
}
