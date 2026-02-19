@extends('frontend.layout.master')

@section('content')
<style>
/* Banner */
.career-banner { background: #024430; padding: 50px 20px; color: #fff; text-align: center; }
.career-banner h2 { font-size: 1.8rem; margin-bottom: 8px; font-weight: 700; color: #dd4925; }
.career-banner p { font-size: 1rem; opacity: 0.9; }

/* Section */
.career-section { background: #f9f9f9; padding: 60px 20px; }
.career-header { text-align: center; margin-bottom: 40px; }
.career-header .subtitle { color: #555; font-size: 14px; font-weight: 600; letter-spacing: 1px; margin-bottom: 8px; }
.career-header h2 { font-size: 2rem; font-weight: 700; margin-bottom: 12px; color: #0a1f44; }
.career-header .description { color: #444; font-size: 15px; max-width: 700px; margin: 0 auto; }

.career-content { display: flex; gap: 40px; max-width: 1100px; margin: 0 auto 40px; }
.career-right { flex: 3; display: flex; flex-direction: column; gap: 16px; }
.career-card { background: #fff; padding: 18px 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
.career-card .label { font-weight: 600; color: #0a1f44; font-size: 15px; }
.career-card .value { font-size: 15px; color: #333; font-weight: 500; }

/* Apply Form */
.apply-wrapper { display: flex; justify-content: center; align-items: center; padding: 60px 20px; }
.apply-card { background: #fff; border-radius: 8px; padding: 40px 30px; box-shadow: 0 4px 14px rgba(0,0,0,0.08); max-width: 60%; width: 100%; }
.apply-card h3 { font-size: 1.8rem; color: #ff5a00; font-weight: 700; text-align: center; margin-bottom: 30px; }
.apply-card form input,
.apply-card form select,
.apply-card form textarea { width: 100%; border: none; border-bottom: 1px solid #ccc; padding: 10px 5px; font-size: 14px; margin-bottom: 24px; background: transparent; outline: none; transition: border-color 0.2s; }
.apply-card form input:focus,
.apply-card form select:focus,
.apply-card form textarea:focus { border-color: #ff5a00; }

.upload-box { border: 1.5px dashed #ccc; border-radius: 6px; padding: 25px; text-align: center; margin-bottom: 10px; cursor: pointer; transition: border-color 0.3s; }
.upload-box:hover { border-color: #ff5a00; }
.upload-box input { display: none; }
.upload-box span { font-size: 14px; color: #666; }

.note { font-size: 12px; color: #888; margin-bottom: 18px; text-align: center; }
.apply-card button { width: 100%; background: #024430; color: #fff; padding: 14px; border: none; border-radius: 4px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.3s; }
.apply-card button:hover { background: #046b4f; }

@media(max-width: 768px) {
  .career-content { flex-direction: column; gap: 20px; }
  .apply-card { max-width: 100%; }
}
</style>

<main class="main">
  <section class="career-banner">
    <h2>JOIN OUR DEVELOPER TEAM</h2>
    <p>Be part of a growing, innovative, and collaborative environment.</p>
  </section>

  <section class="career-section">
    <div class="career-header">
      <p class="subtitle">COME JOIN US</p>
      <h2>Career Openings</h2>
      <p class="description">We’re always looking for creative, talented self-starters to join. Check out our open roles below and fill out an application.</p>
    </div>
    <div class="career-content">
      <div class="career-right">
        <div class="career-card"><span class="label">Open Position</span><span class="value">Meta Developer</span></div>
        <div class="career-card"><span class="label">Experience Required</span><span class="value">2+ Years (Independent)</span></div>
        <div class="career-card"><span class="label">Preference</span><span class="value">Knowledge of GraphQL, WhatsApp Business Cloud API, Webhooks, REST API</span></div>
      </div>
    </div>
  </section>

  <div class="apply-wrapper">
    <div class="apply-card">
      <h3>Apply Now</h3>
      <form id="candidateForm" method="POST" action="{{ route('apply.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="experience" required>
          <option value="">Years of Experience</option>
          <option value="0-1 Years">0-1 Years</option>
          <option value="1-3 Years">1-3 Years</option>
          <option value="3-5 Years">3-5 Years</option>
          <option value="5+ Years">5+ Years</option>
        </select>
        <select name="salary" required>
          <option value="">Salary Expectations (per month)</option>
          <option value="₹20,000 – ₹30,000">₹20,000 – ₹30,000</option>
          <option value="₹30,000 – ₹40,000">₹30,000 – ₹40,000</option>
          <option value="₹40,000 – ₹50,000">₹40,000 – ₹50,000</option>
          <option value="₹50,000+">₹50,000+</option>
        </select>
        <input type="tel" name="phone" placeholder="Phone" required>
        <select name="position" required>
          <option value="">Position Applying For</option>
          <option value="Meta Developer">Meta Developer</option>
        </select>
        <input type="text" name="message" placeholder="Why should we hire you?">
        <label class="upload-box">
          <input type="file" name="resume">
          <span>📎 Upload Resume</span>
        </label>
        <p class="note">Attach file. File size should not exceed 10MB</p>
        <button type="submit">Apply Now</button>
      </form>
    </div>
  </div>
</main>
@endsection
