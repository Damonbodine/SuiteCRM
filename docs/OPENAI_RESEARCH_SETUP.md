# OpenAI Deep Research Setup Instructions

## 🎉 Good News!
Your system already has OpenAI configured and working! The Deep Research feature will use the same OpenAI API key that's currently powering your AI Winnability Analysis.

## 🔬 What is OpenAI Deep Research?
OpenAI Deep Research is an advanced AI system that autonomously:
- **Decomposes complex queries** into research steps
- **Performs web searches** for current legal information
- **Analyzes massive amounts** of legal text, cases, and documents
- **Synthesizes findings** into comprehensive, verifiable reports
- **Uses advanced reasoning** powered by OpenAI's o3 model

## ⚙️ Current Configuration Status
- ✅ OpenAI API Key: Already configured
- ✅ API Integration: Working (used by AI Winnability feature)
- ✅ Model: Will auto-upgrade to `o3-deep-research-2025-06-26` for comprehensive analysis

## 🚀 Quick Activation (2 minutes)

### Step 1: Ensure Real API Mode
Your current configuration shows:
```php
'mock_mode' => false,  // ✅ Good - real API is enabled
'openai_api_key' => 'sk-proj-...',  // ✅ API key is configured
'enabled' => true,  // ✅ AI features are enabled
```

### Step 2: Run Quick Repair
1. Go to **Admin → Repair → Quick Repair & Rebuild**
2. This registers the new OpenAI Research entry point

### Step 3: Test the Feature
1. Navigate to any **Case detail view**
2. Look for the **"🔬 OpenAI Deep Research"** button (green button next to the AI Winnability button)
3. Click it to open the research modal
4. Try a sample query like: *"Fourth Amendment vehicle search precedents for DUI cases"*

## 🔧 Optional: Choose Deep Research Model

The system automatically uses the best available Deep Research model, but you can specify:

**Edit your `config_override.php` file:**
```php
// For highest quality (slower, more expensive)
$sugar_config['ai_status_intelligence']['openai_model'] = 'o3-deep-research-2025-06-26';

// For faster results (still very high quality)
$sugar_config['ai_status_intelligence']['openai_model'] = 'o4-mini-deep-research-2025-06-26';
```

**Cost comparison:**
- GPT-3.5 Turbo: ~$0.002 per request (automatically upgraded to Deep Research)
- OpenAI Deep Research: ~$2-5 per request (significantly more expensive but comprehensive analysis)
- Deep Research includes web search and advanced reasoning capabilities

## 🎯 Research Types Available

The Deep Research feature provides 6 specialized research types:

1. **General Legal Research** - Comprehensive legal analysis
2. **Constitutional Law** - Fourth Amendment, Due Process, etc.
3. **Case Precedents** - Similar cases and outcomes
4. **Sentencing & Mitigation** - Alternative sentencing options
5. **Motion Practice** - Suppression motions, dismissals
6. **Evidence Issues** - Admissibility, chain of custody

## 📊 Usage & Rate Limits

- **Rate Limit**: 5 deep research requests per hour per user
- **Query Length**: Up to 3,000 characters for detailed queries
- **Processing Time**: 5-10 minutes for comprehensive analysis (includes web search and reasoning)
- **Model Used**: Automatically uses OpenAI Deep Research models (o3/o4-mini)
- **Web Search**: Enabled automatically for current legal information

## 🛡️ Security Features

- ✅ Uses your existing secure OpenAI configuration
- ✅ CSRF protection
- ✅ Rate limiting to prevent abuse
- ✅ Permission-based access control
- ✅ Audit logging of all research activities
- ✅ PII sanitization in research queries

## 🔍 Sample Research Queries

**Constitutional Issues:**
> "Analyze Fourth Amendment protections during traffic stops in DUI cases, focusing on reasonable suspicion standards and potential suppression motion opportunities"

**Case Precedents:**
> "Find similar criminal defense cases involving [charge type] in [jurisdiction], focusing on successful defense strategies and favorable outcomes"

**Sentencing Mitigation:**
> "Research mitigation factors for [charge type], including alternative sentencing options, treatment programs, and comparative sentences"

## ❓ Troubleshooting

**Button doesn't appear:**
- Check that you've run Quick Repair & Rebuild
- Verify you have access to view/edit Cases

**"Service not enabled" error:**
- Ensure `mock_mode => false` in your config
- Verify OpenAI API key is present and valid

**Research fails:**
- Check your OpenAI API account has available credits
- Verify API key permissions include chat completions

## 💡 Tips for Best Results

1. **Be Specific**: Include case type, jurisdiction, and specific legal issues
2. **Provide Context**: The system automatically includes case details for context
3. **Use Follow-ups**: Use the "Refine Research" button for deeper analysis
4. **Save Results**: Copy important research to case notes for future reference

---

**Support**: If you encounter any issues, check the SuiteCRM logs under `logs/suitecrm.log` for detailed error messages.