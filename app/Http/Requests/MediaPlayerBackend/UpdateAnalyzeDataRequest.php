<?php

    namespace App\Http\Requests\MediaPlayerBackend;

    use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Http\Exceptions\HttpResponseException;
    use Illuminate\Support\Facades\Log;

    class UpdateAnalyzeDataRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            return true;
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules(): array
        {
            return [
                'uid' => 'required|string',
                'tags' => 'nullable|string',
                'color' => 'nullable|string',
            ];
        }

        /**
         * Get the error messages for the defined validation rules.
         *
         * @return array
         */
        public function messages(): array
        {
            return [
                'uid.required' => 'The UID is required.',
                'uid.string'   => 'The UID must be a string.',
                'tags.string'   => 'The tags must be a string.',
                'color.string'   => 'The color must be a string.',
            ];
        }

        /**
         * Handle a failed validation attempt.
         *
         * @param Validator $validator
         *
         * @return void
         */
        protected function failedValidation(Validator $validator)
        {
            $errors = $validator->errors()->all();
            $errorCount = count($errors);
            $displayedErrors = array_slice($errors, 0, 1); // Display only the first error
            $additionalErrorCount = $errorCount - count($displayedErrors);

            $message = implode(' ', $displayedErrors);
            if ($additionalErrorCount > 0) {
                $message .= " (and $additionalErrorCount more error" . ($additionalErrorCount > 1 ? 's' : '') . ")";
            }

            $response = [
                'result'  => false,
                'status'  => 'failed',
                'message' => $message,
                'errors'  => $validator->errors()->toArray(),
            ];

            Log::error(json_encode($response));
            Log::error('Request data: ' . json_encode($this->all()));


            throw new HttpResponseException(response()->json(
                $response, 422
            ));
        }
    }
