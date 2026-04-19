<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TermsSettingsRequest;
use App\Http\Requests\Dashboard\PrivacySettingsRequest;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GeneralSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show-settings', ['only' => ['generalSettings']]);
        $this->middleware('permission:edit-settings', ['only' => ['store', 'updateTerms', 'updatePrivacy']]);
    }

    public function generalSettings(){
        return view('dashboard.settings.general_settings');
    }

    public function store(Request $request){
        if($request->has('generalSettings')){
            $rules = [
              'type.website_name_ar' => ['required','min:5','max:100'],
              'type.website_name_en' => ['required','min:5','max:100'],
              'type.project_brief_ar' => ['nullable','string','max:5000'],
              'type.project_brief_en' => ['nullable','string','max:5000'],
              'type.seat_price' => ['required','numeric','min:0'],
              'type.completed_deals_commission' => ['nullable','numeric','min:0'],
              'type.commercial_register' => ['required','numeric'],
              'type.tax_number' => ['required','numeric'],
              'type.contact_number'=> ['required','min:9','max:12'],
              'type.copy_right'=> ['required','min:9'],
              'logo1' => [Rule::requiredIf(!GeneralSetting::getValueForKey('logo1')),'image'],
              'favicon2' => [Rule::requiredIf(!GeneralSetting::getValueForKey('favicon2')),'image'],
            ];

            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()){
                return back()->withInput()->withErrors($validator->errors());
            }
            foreach ($request->type as $index => $value){
                GeneralSetting::updateOrCreate(['key' => $index],['value' => $value]);
            }
            if ($request->copyright){
                GeneralSetting::updateOrCreate(['key' => 'copyright'],['value' => $request->copyright]);
            }
            if ($request->hasFile('logo1')){
                $file = $request->file('logo1');
                $filename = 'logo.'.$file->getClientOriginalExtension();
                $file->move(public_path('Site/assets/images/logo'), $filename);
                GeneralSetting::updateOrCreate(['key' => 'logo1'],['value' => $filename]);
            }

            if ($request->hasFile('favicon2')){
                $file = $request->file('favicon2');
                $filename = 'favicon.'.$file->getClientOriginalExtension();
                $file->move(public_path('Site/assets/images/logo'), $filename);
                GeneralSetting::updateOrCreate(['key' => 'favicon2'],['value' => $filename]);
            }

        }

        if ($request->has('socials') || $request->has('titles')){
            $rules = [
              'type.*' => $request->socials?['required','url','active_url']:['required','min:3','max:400'],
            ];
            $validator = Validator::make($request->all(),$rules);

            if ($validator->fails()){
                return back()->withInput()->withErrors($validator->errors());
            }
            foreach ($request->type as $index => $value){
                GeneralSetting::updateOrCreate(['key' => $index],['value' => $value]);
            }
        }

        if ($request->has('background')){

            $rules = [
                'pages_header_image5' => [Rule::requiredIf(GeneralSetting::getValueForKey('pages_header_image5') == null),'image'],
                'login_page_image3' => [Rule::requiredIf(GeneralSetting::getValueForKey('login_page_image3') == null),'image'],
            ];

            $request->validate($rules);


            if ($request->hasFile('login_page_image3')){
                $file = $request->file('login_page_image3');
                $filename = 'login_page_image3.'.$file->getClientOriginalExtension();
                $file->move(public_path('Site/assets/images/header'), $filename);
                GeneralSetting::updateOrCreate(['key' => 'login_page_image3'],['value' => $filename]);
            }

            if ($request->hasFile('pages_header_image5')){
                $file = $request->file('pages_header_image5');
                $filename = 'pages_header_image5.'.$file->getClientOriginalExtension();
                $file->move(public_path('Site/assets/images/header'), $filename);
                GeneralSetting::updateOrCreate(['key' => 'pages_header_image5'],['value' => $filename]);
            }

            return response()->json(['success' => __('dashboard.item updated successfully'),'url' => route('admin.generalSetting.index')]);
        }

        return back()->with(['success' => __('dashboard.item updated successfully')]);
    }

    /**
     * Update terms and conditions.
     *
     * @param TermsSettingsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTerms(TermsSettingsRequest $request)
    {
        $validated = $request->validated();

        if (isset($validated['terms_ar'])) {
            GeneralSetting::updateOrCreate(['key' => 'terms_ar'], ['value' => $validated['terms_ar']]);
        }

        if (isset($validated['terms_en'])) {
            GeneralSetting::updateOrCreate(['key' => 'terms_en'], ['value' => $validated['terms_en']]);
        }

        return redirect(route('admin.generalSetting.index') . '#terms')->with(['success' => __('dashboard.item updated successfully')]);
    }

    /**
     * Update privacy policy.
     *
     * @param PrivacySettingsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePrivacy(PrivacySettingsRequest $request)
    {
        $validated = $request->validated();

        if (isset($validated['privacy_policy_ar'])) {
            GeneralSetting::updateOrCreate(['key' => 'privacy_policy_ar'], ['value' => $validated['privacy_policy_ar']]);
        }

        if (isset($validated['privacy_policy_en'])) {
            GeneralSetting::updateOrCreate(['key' => 'privacy_policy_en'], ['value' => $validated['privacy_policy_en']]);
        }

        return redirect(route('admin.generalSetting.index') . '#privacy')->with(['success' => __('dashboard.item updated successfully')]);
    }
}
