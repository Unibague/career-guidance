import {checkIfModelHasEmptyProperties, toObjectRequest} from "@/HelperFunctions";

export default class Question {
    toObjectRequest() {
        return toObjectRequest(this);
    }

    hasEmptyProperties() {
        return checkIfModelHasEmptyProperties(this);
    }

    getPossibleTypes() {
        return [
            {
                value: 'multiple',
                placeholder: 'Selección múltiple'
            },
        ];
    }

    static fromModel(model) {

        // console.log(model, "Modelo para copiar pregunta")

        return new Question(model.type ?? 'multiple', model.name, model.options, model.program_code);
    }

    static fromRequest(questions) {
        if (!questions) {
            return [];
        }

        let questionObjects = [];
        questions.forEach((question) => {
            questionObjects.push(Question.fromModel(question));
        });
        return questionObjects;
    }

    constructor(type, name, options = [], programCode = null, id = null) {
        this.type = type;
        this.name = name;
        this.options = options;
        this.program_code = programCode;

        if (id === null) {
            this.id = Math.random().toString(16).slice(2);
        }

        this.dataStructure = {
            type: 'required',
            name: 'required',
            options: 'required',
            program_code: 'required',
        };
    }
}
