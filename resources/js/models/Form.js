import {checkIfModelHasEmptyProperties, toObjectRequest} from "@/HelperFunctions";

export default class Form {

    toObjectRequest() {
        return toObjectRequest(this);
    }

    hasEmptyProperties() {
        return checkIfModelHasEmptyProperties(this);
    }

    static copy(form) {
        return new Form(form.id, form.name, form.description);
    }

    static createFormsFromArray(models) {
        let forms = []
        models.forEach(function (model) {
            forms.push(Form.fromModel(model));
        })
        return forms;
    }

    static fromModel(model) {
        return new Form(model.id, model.name, model.description);
    }

    constructor(id = null, name = '', description = '') {
        this.id = id;
        this.name = name;
        this.description = description;

        this.dataStructure = {
            id: null,
            name: 'required',
            description: null,

        }
    }
}
